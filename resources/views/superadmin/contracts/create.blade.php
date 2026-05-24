@extends('superadmin.layout')

@section('title', 'Nuevo Contrato')

@push('styles')
<style>
    .variable-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        cursor: pointer;
        transition: all 0.15s;
        border: 1px solid transparent;
    }
    .variable-chip:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .variable-chip.dark:hover { box-shadow: 0 2px 8px rgba(255,255,255,0.1); }
    .clause-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 20px;
        transition: all 0.2s;
    }
    .dark .clause-card {
        background: #0a0a0a;
        border-color: #262626;
    }
    .clause-card:focus-within {
        border-color: #374151;
        box-shadow: 0 0 0 3px rgba(55,65,81,0.08);
    }
    .dark .clause-card:focus-within {
        border-color: #525252;
        box-shadow: 0 0 0 3px rgba(82,82,82,0.12);
    }
    .sidebar-category { cursor: pointer; transition: all 0.15s; }
    .sidebar-category.active { background: #f3f4f6; }
    .dark .sidebar-category.active { background: #1a1a1a; }
    textarea.clause-content { min-height: 100px; line-height: 1.7; resize: vertical; }
    .preview-panel { background: white; border: 1px solid #e5e7eb; }
    .dark .preview-panel { background: #0a0a0a; border-color: #262626; }
</style>
@endpush

@section('content')
<div class="flex h-[calc(100vh-60px)]">
    <!-- Sidebar Variables -->
    <div class="w-64 flex-shrink-0 border-r border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-950/50 overflow-y-auto hidden lg:block">
        <div class="p-4">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200 dark:border-gray-800">
                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                <span class="text-xs font-bold uppercase tracking-widest text-gray-900 dark:text-white">Datos disponibles</span>
            </div>

            @foreach($variables as $categoryKey => $category)
                <div class="sidebar-category-group mb-1" data-category="{{ $categoryKey }}">
                    <button type="button"
                            onclick="toggleCategory('{{ $categoryKey }}')"
                            class="sidebar-category w-full flex items-center gap-2 px-3 py-2.5 rounded-lg text-left {{ $loop->first ? 'active' : '' }}">
                        @php
                            $iconMap = [
                                'user' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                                'wallet' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
                                'credit-card' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
                                'alert-triangle' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                                'building-2' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                                'file-text' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                            ];
                        @endphp
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconMap[$category['icon']] ?? $iconMap['file-text'] }}"/></svg>
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 flex-1">{{ $category['label'] }}</span>
                        <svg class="w-3 h-3 text-gray-400 category-chevron transition-transform {{ $loop->first ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="variable-list pl-9 pr-2 py-1 space-y-1 {{ $loop->first ? '' : 'hidden' }}" id="vars-{{ $categoryKey }}">
                        @foreach($category['variables'] as $varKey => $varLabel)
                            <span class="variable-chip bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300 dark:border-blue-500/20"
                                  onclick="insertVariable('[{{ $varKey }}]')"
                                  title="{{ $varLabel }}">{{ $varKey }}</span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-y-auto">
        <form id="contractForm" action="{{ route('superadmin.contracts.store') }}" method="POST" class="max-w-4xl mx-auto px-5 md:px-10 py-8">
            @csrf

            <!-- Header -->
            <div class="flex items-end justify-between mb-8">
                <div class="flex-1 max-w-md">
                    <p class="text-[10px] text-gray-500 dark:text-gray-600 uppercase tracking-widest mb-1">Panel Administrativo</p>
                    <input type="text" name="name" value="" required placeholder="Nombre del contrato..."
                           class="w-full text-xl md:text-2xl font-bold bg-transparent border-none outline-none text-gray-900 dark:text-white placeholder:text-gray-300 dark:placeholder:text-gray-600 p-0">
                    <input type="text" name="description" value="" placeholder="Descripción opcional..."
                           class="w-full mt-2 text-sm bg-transparent border-none outline-none text-gray-500 dark:text-gray-400 placeholder:text-gray-300 dark:placeholder:text-gray-700 p-0">
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" onclick="openMobileVars()" class="lg:hidden flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        Variables
                    </button>
                    <button type="submit" class="flex items-center gap-2 px-5 py-2.5 bg-black text-white dark:bg-white dark:text-black rounded-xl text-xs font-bold uppercase tracking-widest hover:opacity-80 transition-opacity">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Guardar
                    </button>
                </div>
            </div>

            <!-- Clauses Section -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white">Cláusulas</h2>
                        <span id="clause-count" class="text-[10px] font-mono text-gray-400">1</span>
                    </div>
                    <button type="button" onclick="addClause()" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-50 text-teal-700 dark:bg-teal-500/10 dark:text-teal-400 text-[10px] font-bold uppercase tracking-wider hover:bg-teal-100 dark:hover:bg-teal-500/20 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Agregar cláusula
                    </button>
                </div>

                <div id="clauses-container" class="space-y-4">
                    <div class="clause-card" data-index="0">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-[10px] font-bold text-gray-500 dark:text-gray-400">1</span>
                                <input type="text" name="clauses[0][title]" value=""
                                       placeholder="Título de la cláusula (ej: Primera, Segunda, Objeto...)"
                                       class="flex-1 text-sm font-bold bg-transparent border-none outline-none text-gray-900 dark:text-white placeholder:text-gray-300 dark:placeholder:text-gray-600 p-0 min-w-0">
                            </div>
                            <div class="flex items-center gap-1 ml-3">
                                <button type="button" onclick="moveClause(0, -1)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors opacity-30 pointer-events-none" title="Subir">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                </button>
                                <button type="button" onclick="moveClause(0, 1)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors opacity-30 pointer-events-none" title="Bajar">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7 7"/></svg>
                                </button>
                                <button type="button" onclick="removeClause(this)" class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 text-gray-400 hover:text-red-500 transition-colors" title="Eliminar">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                        <textarea name="clauses[0][content]"
                                  class="clause-content w-full bg-transparent border-none outline-none text-sm text-gray-700 dark:text-gray-300 placeholder:text-gray-300 dark:placeholder:text-gray-600 p-0 leading-relaxed"
                                  placeholder="Escribe el contenido de esta cláusula aquí... Usa [nombre_completo], [dni], etc. para insertar variables."
                                  rows="4" onfocus="activeTextarea=this" oninput="updateStats(this)"></textarea>
                        <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-800/50 flex items-center justify-between">
                            <span class="text-[10px] text-gray-400 word-count">0 palabras</span>
                            <span class="text-[10px] text-gray-400 var-count">0 variables</span>
                        </div>
                    </div>
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
        <div class="flex-1 overflow-y-auto p-4 space-y-2" id="mobile-vars-content">
            @foreach($variables as $categoryKey => $category)
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">{{ $category['label'] }}</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($category['variables'] as $varKey => $varLabel)
                            <span class="variable-chip bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300 dark:border-blue-500/20"
                                  onclick="insertVariable('[{{ $varKey }}]'); closeMobileVars();"
                                  title="{{ $varLabel }}">{{ $varKey }}</span>
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
let clauseIndex = 1;
let activeTextarea = null;

document.querySelectorAll('.clause-content').forEach(function(ta) {
    ta.addEventListener('focus', function() { activeTextarea = this; });
    ta.addEventListener('input', function() { updateStats(this); });
});

function addClause() {
    const container = document.getElementById('clauses-container');
    const div = document.createElement('div');
    div.className = 'clause-card';
    div.dataset.index = clauseIndex;
    div.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2 flex-1 min-w-0">
                <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-[10px] font-bold text-gray-500 dark:text-gray-400">${clauseIndex + 1}</span>
                <input type="text" name="clauses[${clauseIndex}][title]" value=""
                       placeholder="Título de la cláusula (ej: Primera, Segunda, Objeto...)"
                       class="flex-1 text-sm font-bold bg-transparent border-none outline-none text-gray-900 dark:text-white placeholder:text-gray-300 dark:placeholder:text-gray-600 p-0 min-w-0">
            </div>
            <div class="flex items-center gap-1 ml-3">
                <button type="button" onclick="moveClause(${clauseIndex}, -1)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" title="Subir">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                </button>
                <button type="button" onclick="moveClause(${clauseIndex}, 1)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" title="Bajar">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7 7"/></svg>
                </button>
                <button type="button" onclick="removeClause(this)" class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 text-gray-400 hover:text-red-500 transition-colors" title="Eliminar">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>
        <textarea name="clauses[${clauseIndex}][content]"
                  class="clause-content w-full bg-transparent border-none outline-none text-sm text-gray-700 dark:text-gray-300 placeholder:text-gray-300 dark:placeholder:text-gray-600 p-0 leading-relaxed"
                  placeholder="Escribe el contenido de esta cláusula aquí... Usa [nombre_completo], [dni], etc. para insertar variables."
                  rows="4" onfocus="activeTextarea=this" oninput="updateStats(this)"></textarea>
        <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-800/50 flex items-center justify-between">
            <span class="text-[10px] text-gray-400 word-count">0 palabras</span>
            <span class="text-[10px] text-gray-400 var-count">0 variables</span>
        </div>`;
    container.appendChild(div);
    renumberClauses();
    clauseIndex++;
}

function removeClause(btn) {
    const cards = document.querySelectorAll('.clause-card');
    if (cards.length <= 1) return;
    btn.closest('.clause-card').remove();
    renumberClauses();
}

function moveClause(index, direction) {
    const cards = document.querySelectorAll('.clause-card');
    const card = cards[index];
    if (!card) return;
    const newIndex = index + direction;
    if (newIndex < 0 || newIndex >= cards.length) return;
    if (direction === 1) {
        card.parentNode.insertBefore(card.nextSibling, card);
    } else {
        card.parentNode.insertBefore(card, card.previousSibling);
    }
    renumberClauses();
}

function renumberClauses() {
    const cards = document.querySelectorAll('.clause-card');
    cards.forEach(function(card, i) {
        card.querySelector('span[class*="rounded-full"]').textContent = i + 1;
        const inputs = card.querySelectorAll('input, textarea');
        inputs.forEach(function(input) {
            if (input.name && input.name.includes('clauses[')) {
                input.name = input.name.replace(/clauses\[\d+\]/, 'clauses[' + i + ']');
            }
        });
    });
    document.getElementById('clause-count').textContent = cards.length;
}

function insertVariable(variable) {
    if (!activeTextarea) {
        const firstTa = document.querySelector('.clause-content');
        if (firstTa) { firstTa.focus(); activeTextarea = firstTa; }
        else return;
    }
    const start = activeTextarea.selectionStart;
    const end = activeTextarea.selectionEnd;
    const text = activeTextarea.value;
    activeTextarea.value = text.substring(0, start) + variable + text.substring(end);
    activeTextarea.selectionStart = activeTextarea.selectionEnd = start + variable.length;
    activeTextarea.focus();
    updateStats(activeTextarea);
}

function updateStats(textarea) {
    const card = textarea.closest('.clause-card');
    if (!card) return;
    const words = textarea.value.trim().split(/\s+/).filter(w => w.length > 0).length;
    const vars = (textarea.value.match(/\[/g) || []).length;
    card.querySelector('.word-count').textContent = words + ' palabras';
    card.querySelector('.var-count').textContent = vars + ' variable' + (vars !== 1 ? 's' : '');
}

function toggleCategory(categoryKey) {
    const list = document.getElementById('vars-' + categoryKey);
    const btn = list.closest('.sidebar-category-group').querySelector('.sidebar-category');
    const chevron = btn.querySelector('.category-chevron');
    list.classList.toggle('hidden');
    btn.classList.toggle('active');
    chevron.classList.toggle('rotate-180');
}

function openMobileVars() { document.getElementById('mobile-vars-modal').classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
function closeMobileVars() { document.getElementById('mobile-vars-modal').classList.add('hidden'); document.body.style.overflow = ''; }

function e(str) { const d = document.createElement('div'); d.textContent = str; return d.innerHTML; }
</script>
@endpush
