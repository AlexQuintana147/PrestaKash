@extends('superadmin.layout')

@section('title', $contract ? 'Editar: ' . $contract->name : 'Nuevo Contrato')

@push('styles')
<style>
    .variable-chip {
        display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 6px;
        font-size: 11px; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        cursor: pointer; transition: all 0.15s; border: 1px solid transparent;
    }
    .variable-chip:hover { transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.12); }
    .dark .variable-chip:hover { box-shadow: 0 2px 8px rgba(255,255,255,0.15); }
    .clause-card { background: white; border: 1px solid #e5e7eb; border-radius: 16px; padding: 20px; transition: all 0.2s; }
    .dark .clause-card { background: #0a0a0a; border-color: #262626; }
    .clause-card:focus-within { border-color: #374151; box-shadow: 0 0 0 3px rgba(55,65,81,0.08); }
    .dark .clause-card:focus-within { border-color: #525252; box-shadow: 0 0 0 3px rgba(82,82,82,0.12); }
    .sidebar-category { cursor: pointer; transition: all 0.15s; }
    .sidebar-category.active { background: #f3f4f6; }
    .dark .sidebar-category.active { background: #1a1a1a; }

    /* === Sidebar scrollbar === */
    .sidebar-scroll::-webkit-scrollbar { width: 5px; }
    .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
    .sidebar-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
    .dark .sidebar-scroll::-webkit-scrollbar-thumb { background: #374151; }
    .sidebar-scroll { scrollbar-width: thin; scrollbar-color: #d1d5db transparent; }
    .dark .sidebar-scroll { scrollbar-color: #374151 transparent; }

    .editor-wrapper { position: relative; }
    .editor-textarea { min-height: 100px; line-height: 1.75; resize: vertical; caret-color: #111827; position: relative; z-index: 2; background: transparent !important; color: transparent !important; font-family: inherit; font-size: inherit; }
    .dark .editor-textarea { caret-color: #f3f4f6; }
    .editor-highlight { position: absolute; top: 0; left: 0; right: 0; bottom: 0; padding: inherit; pointer-events: none; white-space: pre-wrap; word-wrap: break-word; overflow-wrap: break-word; line-height: 1.75; font-size: inherit; font-family: inherit; z-index: 1; }
    .editor-highlight .var-token { border-radius: 3px; }

    /* === PREVIEW MODAL === */
    .preview-overlay {
        position: fixed; inset: 0; z-index: 100; display: none;
        background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);
        animation: fadeIn 0.25s ease-out;
    }
    .preview-overlay.active { display: flex; align-items: center; justify-content: center; }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px) scale(0.97); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .preview-modal {
        width: min(92vw, 900px); height: min(90vh, 900px);
        background: white; border-radius: 20px; overflow: hidden;
        display: flex; flex-direction: column;
        box-shadow: 0 25px 80px -12px rgba(0,0,0,0.35), 0 0 0 1px rgba(255,255,255,0.1) inset;
        animation: slideUp 0.35s cubic-bezier(0.16,1,0.3,1);
    }
    .dark .preview-modal { background: #111111; box-shadow: 0 25px 80px -12px rgba(0,0,0,0.7), 0 0 0 1px rgba(255,255,255,0.05) inset; }

    .preview-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 24px; border-bottom: 1px solid #f0f0f0;
        background: linear-gradient(to bottom, #fafafa, #fff);
    }
    .dark .preview-header { border-bottom-color: #222; background: linear-gradient(to bottom, #1a1a1a, #111111); }

    .preview-body { flex: 1; overflow-y: auto; padding: 40px 48px; background: #fdfdfd; }
    .dark .preview-body { background: #0d0d0d; }

    /* === DOCUMENT STYLES === */
    .doc-page {
        max-width: 650px; margin: 0 auto; background: white; padding: 56px 52px 48px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 8px 30px rgba(0,0,0,0.04);
        border: 1px solid #eee; position: relative;
    }
    .dark .doc-page { background: #181818; border-color: #282828; box-shadow: 0 1px 3px rgba(0,0,0,0.2), 0 8px 30px rgba(0,0,0,0.15); }
    .doc-page::before { content: ''; position: absolute; top: 16px; right: 20px; width: 36px; height: 48px; border: 1.5px solid #ddd; border-radius: 2px; opacity: 0.5; }
    .dark .doc-page::before { border-color: #444; }
    .doc-page.has-logo::before { display: none; }
    .doc-logo { position: absolute; top: 12px; right: 20px; max-width: 90px; max-height: 56px; object-fit: contain; }

    .doc-title { text-align: center; font-family: Georgia, 'Times New Roman', serif; font-size: 22px; font-weight: 700; color: #1a1a1a; letter-spacing: 0.02em; margin-bottom: 6px; line-height: 1.35; }
    .dark .doc-title { color: #f0f0f0; }
    .doc-subtitle { text-align: center; font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.18em; color: #888; margin-bottom: 32px; font-weight: 500; }
    .dark .doc-subtitle { color: #666; }
    .doc-divider { height: 1px; background: linear-gradient(to right, transparent, #ccc 20%, #ccc 80%, transparent); margin: 28px 0; border: none; }
    .dark .doc-divider { background: linear-gradient(to right, transparent, #333 20%, #333 80%, transparent); }
    .doc-clause-title { font-family: Georgia, 'Times New Roman', serif; font-size: 14px; font-weight: 700; color: #1a1a1a; margin-bottom: 10px; letter-spacing: 0.01em; }
    .dark .doc-clause-title { color: #e8e8e8; }
    .doc-clause-content { font-family: Georgia, 'Times New Roman', serif; font-size: 13px; line-height: 1.85; color: #333; text-align: justify; hyphens: auto; white-space: pre-wrap; word-wrap: break-word; }
    .dark .doc-clause-content { color: #bbb; }
    .doc-clause { margin-bottom: 22px; }
    .doc-var-inline { font-family: Georgia, 'Times New Roman', serif; font-size: 1em; padding: 1px 5px; border-radius: 3px; font-weight: 600; }

    .doc-footer { margin-top: 36px; padding-top: 20px; border-top: 1px solid #e8e8e8; display: flex; justify-content: space-between; gap: 20px; }
    .dark .doc-footer { border-top-color: #2a2a2a; }
    .doc-signature-block { text-align: center; flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; }
    .doc-signature-label { font-size: 9.5px; text-transform: uppercase; letter-spacing: 0.12em; color: #999; margin-bottom: 28px; font-weight: 600; }
    .dark .doc-signature-label { color: #666; }
    .doc-signature-line { width: 70%; height: 1px; background: #aaa; margin: 0 auto; }
    .dark .doc-signature-line { background: #444; }
    .doc-signature-img { max-height: 50px; margin-bottom: 4px; display: block; margin-left: auto; margin-right: auto; }

    /* === BOX CARD === */
    .box-card {
        display: flex; align-items: center; gap: 14px;
        border-radius: 14px;
        padding: 14px 18px; position: relative; overflow: hidden;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02);
    }
    .box-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06), 0 2px 4px rgba(0,0,0,0.04);
    }
    .box-card::before {
        content: ''; position: absolute; top: 0; left: 0; bottom: 0; width: 4px;
        border-radius: 14px 0 0 14px;
    }
    .box-card-header {
        display: flex; align-items: center; gap: 12px; flex-shrink: 0;
    }
    .box-icon-wrap {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .box-icon-wrap svg { width: 18px; height: 18px; }
    .box-title { font-size: 13px; font-weight: 700; letter-spacing: 0.01em; line-height: 1.3; color: #1f2937; }
    .dark .box-title { color: #1f2937; }
    .box-badge {
        display: inline-flex; align-items: center; gap: 3px;
        padding: 2px 8px; border-radius: 20px;
        font-size: 8.5px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.07em;
    }
    .box-placeholder {
        flex: 1;
        padding-left: 14px;
        border-left: 1px solid rgba(0,0,0,0.06);
        text-align: right;
    }
    .dark .box-placeholder { border-left-color: rgba(255,255,255,0.06); }
    .box-placeholder-text {
        font-size: 12px; line-height: 1.45; color: #6b7280; margin: 0;
    }
    .dark .box-placeholder-text { color: #9ca3af; }
    .box-remove-btn {
        position: absolute; top: 10px; right: 10px; padding: 4px; border-radius: 6px;
        border: none; background: transparent; cursor: pointer;
        color: #9ca3af; transition: all 0.15s; opacity: 0;
    }
    .box-card:hover .box-remove-btn { opacity: 1; }
    .box-remove-btn:hover { background: rgba(239,68,68,0.08); color: #ef4444; }
    .dark .box-remove-btn:hover { background: rgba(239,68,68,0.15); }

    /* === Box Group === */
    .box-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    /* === Box Type Picker === */
    .box-picker-overlay {
        position: fixed; inset: 0; z-index: 90; display: none;
        background: rgba(0,0,0,0.4); backdrop-filter: blur(3px);
        animation: fadeIn 0.2s ease-out;
    }
    .box-picker-overlay.active { display: flex; align-items: center; justify-content: center; }
    .box-picker-modal {
        width: min(92vw, 480px); background: white; border-radius: 20px;
        padding: 24px; box-shadow: 0 20px 60px -12px rgba(0,0,0,0.25);
        animation: slideUp 0.25s cubic-bezier(0.16,1,0.3,1);
    }
    .dark .box-picker-modal { background: #111111; box-shadow: 0 20px 60px -12px rgba(0,0,0,0.6); }
    .box-option {
        display: flex; align-items: center; gap: 14px; padding: 14px 16px; border-radius: 12px;
        cursor: pointer; transition: all 0.15s; border: 1.5px solid transparent; margin-bottom: 8px;
    }
    .box-option:last-child { margin-bottom: 0; }
    .box-option:hover { background: #f8fafc; border-color: #e2e8f0; transform: translateX(4px); }
    .dark .box-option:hover { background: #1a1a1a; border-color: #333; }
    .box-option svg { width: 28px; height: 28px; flex-shrink: 0; color: #6366f1; }
    .dark .box-option svg { color: #818cf8; }
    .preview-empty { text-align: center; padding: 60px 20px; color: #aaa; font-size: 14px; }
    .dark .preview-empty { color: #555; }

    .btn-preview {
        display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 10px;
        font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
        border: 1.5px solid #e0e0e0; background: white; color: #444;
        cursor: pointer; transition: all 0.2s ease;
    }
    .btn-preview:hover { border-color: #bbb; background: #f8f8f8; transform: translateY(-1px); }
    .btn-preview:active { transform: translateY(0); }
    .dark .btn-preview { border-color: #333; background: #161616; color: #aaa; }
    .dark .btn-preview:hover { border-color: #555; background: #222; color: #ddd; }

    /* === RESPONSIVE === */
    @media (max-width: 1024px) {
        .preview-modal { width: min(96vw, 800px); height: min(92vh, 850px); }
        .preview-body { padding: 28px 32px; }
    }
    @media (max-width: 768px) {
        .preview-modal { width: 100vw; height: 100vh; max-height: 100vh; border-radius: 0; }
        .preview-header { padding: 12px 16px; }
        .preview-header h3 { font-size: 13px !important; }
        .preview-body { padding: 20px 16px; overflow-y: auto; -webkit-overflow-scrolling: touch; }
        .btn-preview { padding: 7px 12px; font-size: 10px; }
    }
    @media (max-width: 480px) {
        .preview-body { padding: 16px 12px; }
    }
    @media (max-width: 360px) {
        .preview-header { padding: 10px 12px; }
    }
</style>
@endpush

@section('content')
<div class="flex h-[calc(100vh-60px)]">
    <!-- Sidebar Variables -->
    <div class="w-64 lg:w-72 flex-shrink-0 border-r border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-950/50 overflow-y-auto sidebar-scroll hidden lg:block">
        <div class="p-4">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200 dark:border-gray-800">
                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span class="text-xs font-bold uppercase tracking-widest text-gray-900 dark:text-white">Datos disponibles</span>
            </div>

            @foreach($variables as $categoryKey => $category)
                @php
                    $c = $category['color']; $bgL = $category['bgLight']; $bgD = $category['bgDark'];
                    $tL = $category['textLight']; $tD = $category['textDark'];
                    $bL = $category['borderLight']; $bD = $category['borderDark'];
                @endphp
                <div class="sidebar-category-group mb-1" data-category="{{ $categoryKey }}">
                    <button type="button" onclick="toggleCategory('{{ $categoryKey }}')"
                            class="sidebar-category w-full flex items-center gap-2 px-3 py-2.5 rounded-lg text-left {{ $loop->first ? 'active' : '' }}">
                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:{{ $c }}"></span>
                        @php
                            $iconMap = [
                                'user' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                                'wallet' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
                                'landmark' => 'M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5',
                                'credit-card' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
                                'alert-triangle' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                                'building-2' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                                'file-text' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0120 9V19a2 2 0 01-2 2z',
                                'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                            ];
                        @endphp
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconMap[$category['icon']] ?? $iconMap['file-text'] }}"/></svg>
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 flex-1 truncate">{{ $category['label'] }}</span>
                        <span class="text-[9px] font-mono mr-1 opacity-40 hidden xl:inline">{{ count($category['variables']) }}</span>
                        <svg class="w-3 h-3 text-gray-400 category-chevron transition-transform {{ $loop->first ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="variable-list pl-9 pr-2 py-1 space-y-1 {{ $loop->first ? '' : 'hidden' }}" id="vars-{{ $categoryKey }}">
                        @foreach($category['variables'] as $varKey => $varData)
                            <div class="group">
                                <span class="variable-chip"
                                      style="background:{{ $bgL }};color:{{ $tL }};border-color:{{ $bL }}"
                                      data-dark-style="background:{{ $bgD }};color:{{ $tD }};border-color:{{ $bD }}"
                                      onclick="insertVariable('[{{ $varKey }}]')" title="{{ $varData['desc'] }}">
                                    {{ str_replace('_', ' ', $varKey) }}
                                </span>
                                <p class="text-[9px] text-gray-400 dark:text-gray-600 mt-0.5 ml-0.5 hidden group-hover:block leading-tight">{{ $varData['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-y-auto">
        <form id="contractForm" action="{{ $contract ? route('superadmin.contracts.update', $contract) : route('superadmin.contracts.store') }}" method="POST" class="max-w-4xl mx-auto px-4 sm:px-6 md:px-10 py-6 md:py-8">
            @csrf
            @if($contract) @method('PUT') @endif

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6 md:mb-8">
                <div class="flex-1 max-w-md min-w-0">
                    <p class="text-[10px] text-gray-500 dark:text-gray-600 uppercase tracking-widest mb-1">Panel Administrativo</p>
                    <input type="text" name="name" value="{{ $contract?->name ?? '' }}" required placeholder="Nombre del contrato..."
                           class="w-full text-lg sm:text-xl md:text-2xl font-bold bg-transparent border-none outline-none text-gray-900 dark:text-white placeholder:text-gray-300 dark:placeholder:text-gray-600 p-0 truncate">
                    <input type="text" name="description" value="{{ $contract?->description ?? '' }}" placeholder="Descripción opcional..."
                           class="w-full mt-2 text-sm bg-transparent border-none outline-none text-gray-500 dark:text-gray-400 placeholder:text-gray-300 dark:placeholder:text-gray-700 p-0">
                </div>

                <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0 flex-wrap justify-end">
                    @if($contract)
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="is_active" value="1" {{ $contract?->is_active ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-black dark:bg-gray-800 focus:ring-black dark:focus:ring-white">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Activo</span>
                        </label>
                    @endif
                    <button type="button" onclick="openMobileVars()" class="lg:hidden flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        <span class="hidden xs:inline">Variables</span>
                    </button>
                    <button type="button" onclick="openPreview()" class="btn-preview">
                        <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span class="hidden xs:inline">Vista Previa</span>
                    </button>
                    <button type="submit" class="flex items-center gap-2 px-4 sm:px-5 py-2.5 bg-black text-white dark:bg-white dark:text-black rounded-xl text-xs font-bold uppercase tracking-widest hover:opacity-80 transition-opacity">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Guardar
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 text-sm text-green-500 bg-green-50 dark:bg-green-500/10 px-4 py-3 rounded-xl">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Clauses Section -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0120 9V19a2 2 0 01-2 2z"/></svg>
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white">Cláusulas</h2>
                        <span id="clause-count" class="text-[10px] font-mono text-gray-400">{{ $contract?->clauses?->count() ?? 0 }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="addClause()" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500 text-white hover:bg-emerald-600 transition-colors shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Cláusula
                        </button>
                        <button type="button" onclick="openBoxPicker()" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-violet-500 text-white hover:bg-violet-600 transition-colors shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                            Cuadro
                        </button>
                    </div>
                </div>

                <div id="clauses-container" class="space-y-4">
                    @if($contract && $contract->clauses && $contract->clauses->count() > 0)
                        @php $inBoxGroup = false; @endphp
                        @foreach($contract->clauses as $index => $clause)
                            @if($clause->box_type)
                                @php
                                    $boxTypes = \App\Models\Contract::getBoxTypes();
                                    $boxDef = $boxTypes[$clause->box_type] ?? null;
                                @endphp
                                @if($boxDef)
                                    @if(!$inBoxGroup)
                                        <div class="box-group">
                                        @php $inBoxGroup = true; @endphp
                                    @endif
                                    <div class="box-card" data-box-type="{{ $clause->box_type }}" data-box-index="{{ $index }}"
                                         style="background: linear-gradient(135deg, {{ $boxDef['bgLight'] }} 0%, #ffffff 60%); border: 1px solid {{ $boxDef['borderLight'] }};">
                                        <input type="hidden" name="clauses[{{ $index }}][id]" value="{{ $clause->id }}">
                                        <input type="hidden" class="box-clause-input" name="clauses[{{ $index }}][box_type]" value="{{ $clause->box_type }}">
                                        <input type="hidden" class="box-clause-input" name="clauses[{{ $index }}][title]" value="{{ $clause->title }}">
                                        <input type="hidden" class="box-clause-input" name="clauses[{{ $index }}][content]" value="{{ $clause->content }}">
                                        <button type="button" onclick="removeBox(this)" class="box-remove-btn">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                        <div class="box-card-header">
                                            <div class="box-icon-wrap" style="background:{{ $boxDef['color'] }};color:#fff">
                                                @php
                                                    $iconSvg = '';
                                                    $iconMap = [
                                                        'calendar-days' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
                                                        'banknote' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-1m-2 4H7a2 2 0 01-2-2v-4a2 2 0 012-2h2m2 4h10a2 2 0 002-2v-1"/></svg>',
                                                        'trending-up' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>',
                                                        'user-circle' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
                                                        'clipboard-list' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>',
                                                    ];
                                                @endphp
                                                {!! $iconMap[$boxDef['icon']] ?? '' !!}
                                            </div>
                                            <div>
                                                <p class="box-title">{{ $boxDef['label'] }}</p>
                                                <span class="box-badge" style="background:{{ $boxDef['bgLight'] }};color:{{ $boxDef['textLight'] }};border:1px solid {{ $boxDef['borderLight'] }}">Automático</span>
                                            </div>
                                        </div>
                                        <div class="box-placeholder">
                                            <p class="box-placeholder-text">{{ $boxDef['placeholder_text'] }}</p>
                                        </div>
                                    </div>
                                @endif
                            @else
                                @if($inBoxGroup)
                                    </div>
                                    @php $inBoxGroup = false; @endphp
                                @endif
                                <div class="clause-card" data-index="{{ $index }}">
                                    <input type="hidden" name="clauses[{{ $index }}][id]" value="{{ $clause->id }}">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2 flex-1 min-w-0">
                                            <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-[10px] font-bold text-gray-500 dark:text-gray-400">{{ $index + 1 }}</span>
                                            <input type="text" name="clauses[{{ $index }}][title]" value="{{ $clause->title }}"
                                                   placeholder="Título de la cláusula..."
                                                   class="flex-1 text-sm font-bold bg-transparent border-none outline-none text-gray-900 dark:text-white placeholder:text-gray-300 dark:placeholder:text-gray-600 p-0 min-w-0">
                                        </div>
                                        <div class="flex items-center gap-1 ml-3">
                                            <button type="button" onclick="moveClause(this, -1)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors {{ $index === 0 ? 'opacity-30 pointer-events-none' : '' }}" title="Subir">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                            </button>
                                            <button type="button" onclick="moveClause(this, 1)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors {{ $index === ($contract->clauses->count()-1) ? 'opacity-30 pointer-events-none' : '' }}" title="Bajar">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7 7"/></svg>
                                            </button>
                                            <button type="button" onclick="removeClause(this)" class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 text-gray-400 hover:text-red-500 transition-colors" title="Eliminar">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="editor-wrapper">
                                        <textarea name="clauses[{{ $index }}][content]"
                                                  class="clause-content editor-textarea w-full bg-transparent border-none outline-none text-base text-gray-700 dark:text-gray-300 placeholder:text-gray-300 dark:placeholder:text-gray-600 p-0"
                                                  placeholder="Escribe el contenido..."
                                                  rows="4">{{ $clause->content }}</textarea>
                                        <div class="editor-highlight text-base text-gray-700 dark:text-gray-300"></div>
                                    </div>
                                    <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-800/50 flex items-center justify-between">
                                        <span class="text-[10px] text-gray-400 word-count">{{ Str::wordCount($clause->content) }} palabras</span>
                                        <span class="text-[10px] text-gray-400 var-count">{{ preg_match_all('/\[[a-z_]+\]/i', $clause->content) }} variable{{ preg_match_all('/\[[a-z_]+\]/i', $clause->content) !== 1 ? 's' : '' }}</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        @if($inBoxGroup)</div>@endif
                    @else
                        <div class="clause-card" data-index="0">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-[10px] font-bold text-gray-500 dark:text-gray-400">1</span>
                                    <input type="text" name="clauses[0][title]" value=""
                                           placeholder="Título de la cláusula..."
                                           class="flex-1 text-sm font-bold bg-transparent border-none outline-none text-gray-900 dark:text-white placeholder:text-gray-300 dark:placeholder:text-gray-600 p-0 min-w-0">
                                </div>
                                <div class="flex items-center gap-1 ml-3">
                                    <button type="button" onclick="moveClause(this, -1)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors opacity-30 pointer-events-none" title="Subir">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    </button>
                                    <button type="button" onclick="moveClause(this, 1)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors opacity-30 pointer-events-none" title="Bajar">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7 7"/></svg>
                                    </button>
                                    <button type="button" onclick="removeClause(this)" class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 text-gray-400 hover:text-red-500 transition-colors" title="Eliminar">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="editor-wrapper">
                                <textarea name="clauses[0][content]"
                                          class="clause-content editor-textarea w-full bg-transparent border-none outline-none text-base text-gray-700 dark:text-gray-300 placeholder:text-gray-300 dark:placeholder:text-gray-600 p-0"
                                          placeholder="Escribe el contenido..."
                                          rows="4"></textarea>
                                <div class="editor-highlight text-base text-gray-700 dark:text-gray-300"></div>
                            </div>
                            <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-800/50 flex items-center justify-between">
                                <span class="text-[10px] text-gray-400 word-count">0 palabras</span>
                                <span class="text-[10px] text-gray-400 var-count">0 variables</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Save Button -->
            <div class="pb-8">
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-3 bg-black text-white dark:bg-white dark:text-black rounded-xl text-xs font-bold uppercase tracking-widest hover:opacity-80 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Guardar Contrato
                </button>
            </div>
        </form>
    </div>
</div>

<!-- === BOX PICKER MODAL === -->
<div id="box-picker-overlay" class="box-picker-overlay" onclick="if(event.target===this)closeBoxPicker()">
    <div class="box-picker-modal">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                <span class="text-sm font-bold text-gray-900 dark:text-white">Agregar Cuadro Automático</span>
            </div>
            <button onclick="closeBoxPicker()" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="box-options-list"></div>
    </div>
</div>

<!-- === PREVIEW MODAL === -->
<div id="preview-overlay" class="preview-overlay" onclick="if(event.target===this)closePreview()">
    <div class="preview-modal" role="dialog" aria-modal="true" aria-labelledby="preview-modal-title">
        <div class="preview-header">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0120 9V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h3 id="preview-modal-title" class="text-sm font-bold text-gray-900 dark:text-white leading-tight">Vista Previa del Contrato</h3>
                    <p id="preview-contract-name" class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $contract?->name ?? 'Sin título' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="closePreview()" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" aria-label="Cerrar vista previa">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div class="preview-body" id="preview-document">
            <div class="doc-page {{ \App\Models\CompanySetting::get('company_logo') ? 'has-logo' : '' }}" id="doc-page-inner">
                @if($companyLogo = \App\Models\CompanySetting::get('company_logo'))
                    <img src="{{ Storage::url($companyLogo) }}" alt="Logo" class="doc-logo">
                @endif
                <h1 class="doc-title" id="preview-doc-title">{{ $contract?->name ?: 'Contrato sin título' }}</h1>
                <p class="doc-subtitle">Documento de referencia · Vista previa con datos de ejemplo</p>
                <hr class="doc-divider">
                <div id="preview-clauses-content">
                    <div class="preview-empty">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0120 9V19a2 2 0 01-2 2z"/></svg>
                        Agrega cláusulas para ver la vista previa del contrato.
                    </div>
                </div>
                <hr class="doc-divider">
                <div class="doc-footer">
                    <div class="doc-signature-block">
                        @if($companySignature = \App\Models\CompanySetting::get('company_signature'))
                            <img src="{{ Storage::url($companySignature) }}" alt="Firma" class="doc-signature-img">
                        @endif
                        <div class="doc-signature-line"></div>
                        <p class="mt-2 text-[9px] text-gray-400 text-center">Representante Legal</p>
                    </div>
                    <div class="doc-signature-block">
                        <div class="doc-signature-line"></div>
                        <p class="mt-2 text-[9px] text-gray-400 text-center">DNI / Identificación</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Variables Modal -->
<div id="mobile-vars-modal" class="fixed inset-0 z-50 hidden lg:hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeMobileVars()"></div>
    <div class="absolute inset-x-4 bottom-4 top-20 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-800">
            <span class="text-sm font-bold text-gray-900 dark:text-white">Variables disponibles</span>
            <button onclick="closeMobileVars()" class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto sidebar-scroll p-4 space-y-3" id="mobile-vars-content">
            @foreach($variables as $categoryKey => $category)
                @php
                    $c = $category['color']; $bgL = $category['bgLight']; $bgD = $category['bgDark'];
                    $tL = $category['textLight']; $tD = $category['textDark']; $bL = $category['borderLight']; $bD = $category['borderDark'];
                @endphp
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2.5 h-2.5 rounded-full" style="background:{{ $c }}"></span>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">{{ $category['label'] }}</p>
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($category['variables'] as $varKey => $varData)
                            <span class="variable-chip"
                                  style="background:{{ $bgL }};color:{{ $tL }};border-color:{{ $bL }}"
                                  data-dark-style="background:{{ $bgD }};color:{{ $tD }};border-color:{{ $bD }}"
                                  onclick="insertVariable('[{{ $varKey }}]'); closeMobileVars();"
                                  title="{{ $varData['desc'] }}">
                                {{ str_replace('_', ' ', $varKey) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const VARIABLES_MAP = {!! json_encode(\App\Models\Contract::getAllVariablesFlat(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};
const SAMPLE_DATA = {!! json_encode([
    'nombre_completo' => 'Pepe Luna Castillo','nombres' => 'Pepe','apellidos' => 'Luna Castillo',
    'dni' => '12345678','ruc' => '20123456789','razon_social' => 'Luna Castillo E.I.R.L.',
    'email' => 'pepe@correo.com','telefono' => '+51 999 888 777',
    'direccion' => 'Av. Principal 123, Urbanización San Isidro, Lima',
    'estado_civil' => 'Casado','ocupacion' => 'Ingeniero de Sistemas','lugar_trabajo' => 'Tecnologías del Perú S.A.C.',
    'numero_cuenta' => '191-1234567890-0-56','tipo_moneda' => 'Soles','tipo_cuenta' => 'Cuenta de Ahorros','cci' => '00219101234567890056',
    'monto_invertido' => 'S/ 10,000.00','monto_deseado' => 'S/ 15,000.00','tasa_retorno_mensual' => '3.5%',
    'plazo_meses' => '12','cuota_mensual' => 'S/ 350.00','fecha_inicio' => '01 de junio de 2026',
    'fecha_fin' => '01 de junio de 2027','codigo_inversion' => 'OPP-2026-A001',
    'oportunidad_titulo' => 'Fondo de Inversión Comercial Q3-2026','moneda' => 'Soles (PEN)',
    'tipo_amortizacion' => 'Capital e intereses mensuales',
    'prestamo_monto' => 'S/ 5,000.00','prestamo_codigo' => 'PRE-2026-0042','prestamo_plazo' => '6 meses',
    'prestamo_tasa_interes' => '8.5% anual','prestamo_cuota' => 'S/ 867.20',
    'prestamo_fecha_desembolso' => '15 de marzo de 2026','prestamo_motivo' => 'Capital de trabajo para expansión comercial',
    'prestamo_total' => 'S/ 5,203.20',
    'total_pagado' => 'S/ 4,336.80','pagos_realizados' => '5 pagos','pagos_pendientes' => '1 pago',
    'proximo_pago_fecha' => '15 de septiembre de 2026','proximo_pago_monto' => 'S/ 867.20',
    'ultimo_pago_fecha' => '15 de agosto de 2026','ultimo_pago_monto' => 'S/ 867.20',
    'tasa_mora' => '5% mensual','dias_gracia' => '3 días','mora_diaria' => 'S/ 1.45',
    'dias_mora' => '0 días','interes_mora_acumulado' => 'S/ 0.00','penalidad_actual' => 'S/ 0.00',
    'empresa_nombre' => 'PrestaKash Soluciones Financieras S.A.C.','empresa_ruc' => '20601234567',
    'empresa_direccion' => 'Av. Central 456, Oficina 302, Cercado de Lima, Perú',
    'empresa_telefono' => '+51 (01) 111 222 333','empresa_email' => 'contacto@prestakash.com',
    'empresa_representante' => 'Juan Carlos Pérez García','empresa_representante_dni' => '45678901',
    'logo_empresa' => '[LOGO DE LA EMPRESA]','firma_representante' => '[FIRMA DEL REPRESENTANTE]',
    'sello_empresa' => '[SELLO DE LA EMPRESA]',
    'contrato_nombre' => 'Contrato de Inversión','contrato_numero' => 'CT-00001',
    'fecha_firma' => now()->format('d/m/Y'),'ciudad' => 'Lima',
    'firma_cliente' => '[FIRMA DEL CLIENTE]','huella_cliente' => '[HUELLA DIGITAL]',
    'fecha_actual' => now()->format('d/m/Y'),
    'dia_hoy' => now()->format('d'),'mes_hoy' => \App\Models\Contract::monthToText((int)now()->format('m')),'anio_hoy' => now()->format('Y'),
    'dia_letras' => \App\Models\Contract::numberToWords((int)now()->format('d')),'mes_letras' => \App\Models\Contract::monthToText((int)now()->format('m')),'anio_letras' => \App\Models\Contract::numberToWords((int)now()->format('Y')),
    'fecha_completa' => now()->format('d') . ' de ' . \App\Models\Contract::monthToText((int)now()->format('m')) . ' de ' . now()->format('Y'),
    'fecha_completa_letras' => \App\Models\Contract::numberToWords((int)now()->format('d')) . ' de ' . \App\Models\Contract::monthToText((int)now()->format('m')) . ' de ' . \App\Models\Contract::numberToWords((int)now()->format('Y')),
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};

const BOX_TYPES = {!! json_encode(\App\Models\Contract::getBoxTypes(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};

let clauseIndex = {{ max(($contract?->clauses?->count() ?? 0), 0) }};
let activeTextarea = null;

document.querySelectorAll('.variable-chip').forEach(function(chip) {
    if (!chip.dataset.lightStyle) chip.dataset.lightStyle = chip.getAttribute('style');
});

document.querySelectorAll('.clause-content').forEach(function(ta) {
    ta.addEventListener('focus', function() { activeTextarea = this; });
    ta.addEventListener('input', function() { updateHighlight(this); updateStats(this); autoResize(this); });
    ta.addEventListener('scroll', function() { syncScroll(this); });
    updateHighlight(ta);
    autoResize(ta);
});

function activeEditor(textarea) { activeTextarea = textarea; }
function escapeHtml(text) { return text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

function updateHighlight(textarea) {
    const wrapper = textarea.closest('.editor-wrapper');
    if (!wrapper) return;
    const highlight = wrapper.querySelector('.editor-highlight');
    if (!highlight) return;

    let html = escapeHtml(textarea.value);
    html = html.replace(/\[([a-z_]+)\]/gi, function(match, key) {
        const v = VARIABLES_MAP[key.toLowerCase()];
        if (v) {
            const isDark = document.documentElement.classList.contains('dark');
            return '<span class="var-token" style="background:' + (isDark?v.bg_dark:v.bg_light) + ';color:' + (isDark?v.text_dark:v.text_light) + '">' + match + '</span>';
        }
        return '<span class="var-token" style="background:#fef3c7;color:#92400e">' + match + '</span>';
    });

    highlight.innerHTML = html || '';
    highlight.style.fontSize = getComputedStyle(textarea).fontSize;
    highlight.style.fontFamily = getComputedStyle(textarea).fontFamily;
    highlight.style.padding = getComputedStyle(textarea).padding;
    highlight.style.lineHeight = getComputedStyle(textarea).lineHeight;
    syncScroll(textarea);
}

function syncScroll(textarea) {
    const hl = textarea.closest('.editor-wrapper')?.querySelector('.editor-highlight');
    if (hl) { hl.scrollTop = textarea.scrollTop; hl.scrollLeft = textarea.scrollLeft; }
}

function autoResize(textarea) {
    const wrapper = textarea.closest('.editor-wrapper');
    if (!wrapper) return;
    textarea.style.height = 'auto';
    const minHeight = parseInt(getComputedStyle(textarea).minHeight) || 100;
    const scrollHeight = textarea.scrollHeight;
    const newHeight = Math.max(minHeight, scrollHeight);
    textarea.style.height = newHeight + 'px';
    wrapper.style.minHeight = newHeight + 'px';
}

function updateStats(textarea) {
    const card = textarea.closest('.clause-card'); if (!card) return;
    const wc = card.querySelector('.word-count'), vc = card.querySelector('.var-count');
    if (wc) wc.textContent = textarea.value.trim().split(/\s+/).filter(w=>w.length).length + ' palabras';
    if (vc) { const m = textarea.value.match(/\[[a-z_]+\]/gi); vc.textContent = m ? m.length + ' variable' + (m.length!==1?'s':'') : '0 variables'; }
}

function addClause() {
    const container = document.getElementById('clauses-container');
    const div = document.createElement('div');
    clauseIndex++;
    div.className = 'clause-card'; div.dataset.index = clauseIndex;
    div.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2 flex-1 min-w-0">
                <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-[10px] font-bold text-gray-500 dark:text-gray-400">${clauseIndex+1}</span>
                <input type="text" name="clauses[${clauseIndex}][title]" value="" placeholder="Título de la cláusula..." class="flex-1 text-sm font-bold bg-transparent border-none outline-none text-gray-900 dark:text-white placeholder:text-gray-300 dark:placeholder:text-gray-600 p-0 min-w-0">
            </div>
            <div class="flex items-center gap-1 ml-3">
                <button type="button" onclick="moveClause(this,-1)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" title="Subir"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg></button>
                <button type="button" onclick="moveClause(this,1)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" title="Bajar"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7 7"/></svg></button>
                <button type="button" onclick="removeClause(this)" class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 text-gray-400 hover:text-red-500 transition-colors" title="Eliminar"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
            </div>
        </div>
        <div class="editor-wrapper">
            <textarea name="clauses[${clauseIndex}][content]" class="clause-content editor-textarea w-full bg-transparent border-none outline-none text-base text-gray-700 dark:text-gray-300 placeholder:text-gray-300 dark:placeholder:text-gray-600 p-0" placeholder="Escribe el contenido..." rows="4" onfocus="activeEditor(this)" oninput="updateHighlight(this);updateStats(this)"></textarea>
            <div class="editor-highlight text-base text-gray-700 dark:text-gray-300"></div>
        </div>
        <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-800/50 flex items-center justify-between">
            <span class="text-[10px] text-gray-400 word-count">0 palabras</span>
            <span class="text-[10px] text-gray-400 var-count">0 variables</span>
        </div>`;
    container.appendChild(div);
    div.querySelector('.clause-content').addEventListener('scroll', function(){syncScroll(this)});
    renumberClauses();
}

function removeClause(btn) {
    if (document.querySelectorAll('.clause-card').length <= 1) return;
    btn.closest('.clause-card').remove(); renumberClauses();
}

function moveClause(btn, direction) {
    const card = btn.closest('.clause-card');
    if (!card) return;
    const container = card.parentNode;
    if (direction === 1) {
        const next = card.nextElementSibling;
        if (next) container.insertBefore(next, card);
    } else {
        const prev = card.previousElementSibling;
        if (prev) container.insertBefore(card, prev);
    }
    renumberClauses();
}

function removeBox(btn) {
    const card = btn.closest('.box-card');
    const group = card.closest('.box-group');
    card.remove();
    if (group && !group.querySelector('.box-card')) { group.remove(); }
    renumberClauses();
}

function getOrderedClauseCards() {
    const container = document.getElementById('clauses-container');
    const result = [];
    Array.from(container.children).forEach(child => {
        if (child.classList.contains('clause-card')) result.push(child);
        else if (child.classList.contains('box-card')) result.push(child);
        else if (child.classList.contains('box-group')) {
            child.querySelectorAll(':scope > .box-card').forEach(b => result.push(b));
        }
    });
    return result;
}

function renumberClauses() {
    let clauseIdx = 0;
    const clauseCards = document.querySelectorAll('#clauses-container > .clause-card');
    getOrderedClauseCards().forEach((card) => {
        if (card.classList.contains('clause-card')) {
            const numSpan = card.querySelector('span[class*="rounded-full"]');
            if (numSpan) numSpan.textContent = clauseIdx + 1;
            card.querySelectorAll('input,textarea').forEach(input => {
                if (input.name?.includes('clauses[')) input.name = input.name.replace(/clauses\[\d+\]/, 'clauses[' + clauseIdx + ']');
            });
            const upBtn = card.querySelectorAll('button')[0], downBtn = card.querySelectorAll('button')[1];
            if (upBtn) { upBtn.classList.toggle('opacity-30', clauseIdx === 0); upBtn.toggleAttribute('disabled', clauseIdx === 0); }
            if (downBtn) { downBtn.classList.toggle('opacity-30', clauseIdx === clauseCards.length - 1); downBtn.toggleAttribute('disabled', clauseIdx === clauseCards.length - 1); }
            clauseIdx++;
        } else if (card.classList.contains('box-card')) {
            card.querySelectorAll('.box-clause-input').forEach(input => {
                if (input.name?.includes('clauses[')) input.name = input.name.replace(/clauses\[\d+\]|clauses\[_NEW_\]/, 'clauses[' + clauseIdx + ']');
            });
            clauseIdx++;
        }
    });
    document.getElementById('clause-count').textContent = clauseIdx;
}

function insertVariable(variable) {
    if(!activeTextarea){const t=document.querySelector('.clause-content');if(t){t.focus();activeTextarea=t;}else return;}
    const s=activeTextarea.selectionStart,e=activeTextarea.selectionEnd,text=activeTextarea.value;
    activeTextarea.value=text.substring(0,s)+variable+text.substring(e);
    activeTextarea.selectionStart=activeTextarea.selectionEnd=s+variable.length;
    updateHighlight(activeTextarea);updateStats(activeTextarea);activeTextarea.focus();
}

function toggleCategory(catKey) {
    const g=document.querySelector('[data-category="'+catKey+'"]'),l=g?.querySelector('.variable-list'),
          ch=g?.querySelector('.category-chevron'),btn=g?.querySelector('.sidebar-category');
    if(l.classList.contains('hidden')){
        document.querySelectorAll('.variable-list').forEach(el=>el.classList.add('hidden'));
        document.querySelectorAll('.category-chevron').forEach(el=>el.classList.remove('rotate-180'));
        document.querySelectorAll('.sidebar-category').forEach(el=>el.classList.remove('active'));
        l.classList.remove('hidden');ch.classList.add('rotate-180');btn.classList.add('active');
    } else { l.classList.add('hidden');ch.classList.remove('rotate-180');btn.classList.remove('active'); }
}

/* === PREVIEW === */
function openPreview() { renderPreview(); document.getElementById('preview-overlay').classList.add('active'); document.body.style.overflow='hidden'; }
function closePreview() { document.getElementById('preview-overlay').classList.remove('active'); document.body.style.overflow=''; }

/* === BOX PICKER === */
function openBoxPicker() {
    const list = document.getElementById('box-options-list');
    let html = '';
    const isDark = document.documentElement.classList.contains('dark');
    Object.keys(BOX_TYPES).forEach(function(key) {
        const b = BOX_TYPES[key];
        html += '<div class="box-option" onclick="addBox(\'' + key + '\')">';
        html += getBoxIcon(b.icon);
        html += '<div><p style="font-size:12.5px;font-weight:700;color:' + (isDark ? '#f3f4f6' : '#1f2937') + '">' + b.label + '</p>';
        html += '<p style="font-size:10px;color:#9ca3af;margin-top:1px">' + b.description + '</p></div></div>';
    });
    list.innerHTML = html;
    document.getElementById('box-picker-overlay').classList.add('active');
}
function closeBoxPicker() { document.getElementById('box-picker-overlay').classList.remove('active'); }

const BOX_ICONS = {
    'calendar-days': '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
    'banknote': '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-1m-2 4H7a2 2 0 01-2-2v-4a2 2 0 012-2h2m2 4h10a2 2 0 002-2v-1"/></svg>',
    'trending-up': '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>',
    'user-circle': '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
    'clipboard-list': '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>',
};
function getBoxIcon(iconKey) { return BOX_ICONS[iconKey] || ''; }

let boxIndex = 0;

function addBox(typeKey) {
    const b = BOX_TYPES[typeKey];
    if (!b) return;
    closeBoxPicker();
    const container = document.getElementById('clauses-container');
    const isDark = document.documentElement.classList.contains('dark');

    let group = null;
    const lastChild = container.lastElementChild;
    if (lastChild && lastChild.classList.contains('box-group')) {
        group = lastChild;
    } else {
        group = document.createElement('div');
        group.className = 'box-group';
        container.appendChild(group);
    }

    const card = document.createElement('div');
    card.className = 'box-card';
    card.style.background = 'linear-gradient(135deg, ' + b.bgLight + ' 0%, ' + (isDark ? '#0a0a0a' : '#ffffff') + ' 60%)';
    card.style.border = '1px solid ' + b.borderLight;
    card.dataset.boxType = typeKey;
    card.dataset.boxIndex = boxIndex++;
    card.innerHTML =
        '<button type="button" onclick="removeBox(this)" class="box-remove-btn">' +
            '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>' +
        '</button>' +
        '<div class="box-card-header">' +
            '<div class="box-icon-wrap" style="background:' + b.color + ';color:#fff">' + getBoxIcon(b.icon) + '</div>' +
            '<div><p class="box-title">' + escapeHtml(b.label) + '</p>' +
            '<span class="box-badge" style="background:' + b.bgLight + ';color:' + b.textLight + ';border:1px solid ' + b.borderLight + '">Automático</span></div></div>' +
        '<input type="hidden" class="box-clause-input" name="clauses[_NEW_][box_type]" value="' + typeKey + '">' +
        '<input type="hidden" class="box-clause-input" name="clauses[_NEW_][title]" value="">' +
        '<input type="hidden" class="box-clause-input" name="clauses[_NEW_][content]" value="">' +
        '<div class="box-placeholder">' +
            '<p class="box-placeholder-text">' + b.placeholder_text + '</p></div>';
    group.appendChild(card);
    renumberClauses();
}

function renderPreview() {
    const nameInput = document.querySelector('input[name="name"]');
    const docTitle = document.getElementById('preview-doc-title');
    const contractNameEl = document.getElementById('preview-contract-name');
    const contentEl = document.getElementById('preview-clauses-content');

    const contractName = nameInput?.value?.trim() || 'Contrato sin título';
    docTitle.textContent = contractName;
    contractNameEl.textContent = contractName;

    const allCards = document.querySelectorAll('#clauses-container > *');
    if (allCards.length === 0) {
        contentEl.innerHTML = '<div class="preview-empty"><svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0120 9V19a2 2 0 01-2 2z"/></svg>Agrega cláusulas o cuadros para ver la vista previa del contrato.</div>';
        return;
    }

    function renderBoxCard(card) {
        const typeKey = card.dataset.boxType;
        const b = BOX_TYPES[typeKey];
        if (!b) return '';
        const isDark = document.documentElement.classList.contains('dark');
        const bgTo = isDark ? '#0a0a0a' : '#ffffff';
        const divider = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
        const textColor = isDark ? '#9ca3af' : '#6b7280';
        let h = '<div style="display:flex;align-items:center;gap:14px;border-radius:14px;padding:14px 18px;background:linear-gradient(135deg,'+b.bgLight+' 0%,'+bgTo+' 60%);border:1px solid '+b.borderLight+';box-shadow:0 1px 3px rgba(0,0,0,0.04);position:relative">';
        h += '<div style="position:absolute;top:0;left:0;bottom:0;width:4px;background:'+b.color+';border-radius:14px 0 0 14px"></div>';
        h += '<div style="display:flex;align-items:center;gap:12px;flex-shrink:0">';
        h += '<div style="width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:'+b.color+';color:#fff">'+getBoxIcon(b.icon)+'</div>';
        h += '<div><span style="font-size:13px;font-weight:700;color:#1f2937;line-height:1.3">' + escapeHtml(b.label) + '</span> ';
        h += '<span style="display:inline-flex;align-items:center;gap:3px;font-size:8.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;padding:2px 8px;border-radius:20px;background:'+b.bgLight+';color:'+b.textLight+';border:1px solid '+b.borderLight+'">Automático</span></div></div>';
        h += '<div style="flex:1;padding-left:14px;border-left:1px solid '+divider+';text-align:right">';
        h += '<p style="font-size:12px;line-height:1.45;color:'+textColor+';margin:0">' + b.placeholder_text + '</p></div></div>';
        return h;
    }

    let html = '';
    allCards.forEach(card => {
        if (card.classList.contains('box-card')) {
            html += renderBoxCard(card);
        } else if (card.classList.contains('box-group')) {
            const boxes = card.querySelectorAll(':scope > .box-card');
            if (boxes.length) {
                html += '<div style="display:flex;flex-direction:column;gap:12px;margin-bottom:22px">';
                boxes.forEach(box => { html += renderBoxCard(box); });
                html += '</div>';
            }
        } else if (card.classList.contains('clause-card')) {
            const titleInput = card.querySelector('input[name*="[title]"]');
            const textarea = card.querySelector('textarea[name*="[content]"]');
            const title = titleInput?.value?.trim() || 'Sin título';
            const rawContent = textarea?.value?.trim() || '';
            if (!rawContent) return;

            let renderedContent = escapeHtml(rawContent);
            renderedContent = renderedContent.replace(/\[([a-z_]+)\]/gi, function(match, key) {
                const val = SAMPLE_DATA[key.toLowerCase()];
                const v = VARIABLES_MAP[key.toLowerCase()];
                if (val && v.color) {
                    return '<span class="doc-var-inline" style="background:'+v.bgLight+';color:'+v.textLight+';border:1px solid '+v.borderLight+'">'+escapeHtml(val||match)+'</span>';
                }
                return '<span class="doc-var-inline" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a">'+escapeHtml(val||match)+'</span>';
            });
            renderedContent = renderedContent.replace(/\n/g, '<br>');

            html += '<div class="doc-clause">';
            html += '<h4 class="doc-clause-title">' + escapeHtml(title) + '</h4>';
            html += '<div class="doc-clause-content">' + renderedContent + '</div>';
            html += '</div>';
        }
    });

    contentEl.innerHTML = html || '<div class="preview-empty">No hay contenido en las cláusulas.</div>';
}

document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { closePreview(); closeBoxPicker(); } });

function openMobileVars() { document.getElementById('mobile-vars-modal').classList.remove('hidden'); document.body.style.overflow='hidden'; }
function closeMobileVars() { document.getElementById('mobile-vars-modal').classList.add('hidden'); document.body.style.overflow=''; }
</script>
@endpush
