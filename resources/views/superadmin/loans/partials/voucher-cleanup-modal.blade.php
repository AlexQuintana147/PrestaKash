<!-- Modal Limpiar Comprobantes -->
<style>
    #vc_items_scroll {
        scrollbar-width: thin;
        scrollbar-color: rgba(156, 163, 175, 0.35) transparent;
    }

    #vc_items_scroll::-webkit-scrollbar { width: 3px; }
    #vc_items_scroll::-webkit-scrollbar-track { background: transparent; }
    #vc_items_scroll::-webkit-scrollbar-thumb {
        background: rgba(156, 163, 175, 0.35);
        border-radius: 9999px;
    }
    #vc_items_scroll::-webkit-scrollbar-thumb:hover { background: rgba(156, 163, 175, 0.6); }

    .dark #vc_items_scroll { scrollbar-color: rgba(75, 85, 99, 0.55) transparent; }
    .dark #vc_items_scroll::-webkit-scrollbar-thumb { background: rgba(75, 85, 99, 0.55); }
    .dark #vc_items_scroll::-webkit-scrollbar-thumb:hover { background: rgba(75, 85, 99, 0.85); }

    .vc-date-box input[type="date"] {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }

    .vc-date-box input[type="date"]::-webkit-calendar-picker-indicator {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        cursor: pointer;
    }
</style>

<div id="voucherCleanupModal"
    class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-[120] flex items-center justify-center p-4"
    onclick="if(event.target===this) closeVoucherCleanupModal()">
    <div class="bg-white dark:bg-gray-950 w-full max-w-md rounded-2xl border border-gray-100 dark:border-gray-800 shadow-2xl flex flex-col max-h-[90vh]"
        onclick="event.stopPropagation()">

        <div class="relative px-5 pt-5 pb-4 border-b border-gray-100 dark:border-gray-900 flex-shrink-0">
            <button type="button" onclick="closeVoucherCleanupModal()"
                class="absolute top-4 right-4 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <p class="text-[8px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1">Pagadas</p>
            <h3 class="text-base font-black text-gray-900 dark:text-white tracking-tight">Limpiar comprobantes</h3>
        </div>

        <div class="px-5 py-4 space-y-3 flex-shrink-0">
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <span class="text-[8px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest block mb-1.5">Desde</span>
                    <label class="vc-date-box relative flex items-center justify-between gap-2 h-10 px-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/80 cursor-pointer hover:border-gray-300 dark:hover:border-gray-700 transition-colors">
                        <input type="date" id="vc_from_date" onchange="syncVcDateDisplay('from')">
                        <span id="vc_from_display" class="text-xs font-bold text-gray-900 dark:text-white pointer-events-none truncate">—</span>
                        <svg class="w-4 h-4 flex-shrink-0 text-gray-900 dark:text-white pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </label>
                </div>
                <div>
                    <span class="text-[8px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest block mb-1.5">Hasta</span>
                    <label class="vc-date-box relative flex items-center justify-between gap-2 h-10 px-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/80 cursor-pointer hover:border-gray-300 dark:hover:border-gray-700 transition-colors">
                        <input type="date" id="vc_to_date" onchange="syncVcDateDisplay('to')">
                        <span id="vc_to_display" class="text-xs font-bold text-gray-900 dark:text-white pointer-events-none truncate">—</span>
                        <svg class="w-4 h-4 flex-shrink-0 text-gray-900 dark:text-white pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </label>
                </div>
            </div>
            <button type="button" id="vc_preview_btn" onclick="previewVoucherCleanup()"
                class="w-full py-2.5 rounded-lg border border-gray-200 dark:border-gray-800 text-[9px] font-black uppercase tracking-widest text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                Calcular espacio
            </button>
        </div>

        <div id="vc_preview_panel" class="hidden flex-1 flex flex-col min-h-0 border-t border-gray-100 dark:border-gray-900">
            <div class="px-5 py-3 flex-shrink-0 space-y-1">
                <p id="vc_range_label" class="text-[10px] font-bold text-gray-500 dark:text-gray-400"></p>
                <p class="text-xs font-black text-gray-900 dark:text-white">
                    <span id="vc_size_label" class="text-green-600 dark:text-green-400"></span>
                    <span class="text-gray-400 dark:text-gray-500 mx-1">·</span>
                    <span id="vc_count_label"></span>
                </p>
            </div>
            <div id="vc_items_scroll" class="flex-1 overflow-y-auto px-5 pb-3 min-h-0" style="max-height: 200px;">
                <p class="text-[8px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2 sticky top-0 bg-white dark:bg-gray-950 py-1 z-10">
                    Se eliminarán · click para ver
                </p>
                <ul id="vc_items_list" class="space-y-1.5"></ul>
                <p id="vc_empty_msg" class="hidden text-[10px] text-gray-500 dark:text-gray-400 text-center py-8">Sin comprobantes en este rango.</p>
            </div>
        </div>

        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-900 flex items-center justify-between flex-shrink-0">
            <button type="button" onclick="closeVoucherCleanupModal()"
                class="text-[9px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                Cancelar
            </button>
            <form id="vc_cleanup_form" method="POST" action="{{ route('superadmin.installments.vouchers.cleanup') }}">
                @csrf
                <input type="hidden" name="from_date" id="vc_form_from">
                <input type="hidden" name="to_date" id="vc_form_to">
                <button type="button" id="vc_confirm_btn" disabled onclick="openVcConfirmModal()"
                    class="text-[9px] font-black uppercase tracking-widest text-red-500 dark:text-red-400 border-b border-red-400 dark:border-red-500/60 pb-px hover:opacity-60 transition-opacity disabled:opacity-25 disabled:cursor-not-allowed disabled:border-gray-300 dark:disabled:border-gray-700 disabled:text-gray-400 dark:disabled:text-gray-600">
                    Confirmar limpieza
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Confirmación limpieza -->
<div id="vcConfirmModal"
    class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-[130] flex items-center justify-center p-4"
    onclick="if(event.target===this) closeVcConfirmModal()">
    <div class="bg-white dark:bg-gray-950 w-full max-w-sm rounded-2xl border border-gray-100 dark:border-gray-800 shadow-2xl p-6"
        onclick="event.stopPropagation()">
        <div class="w-9 h-9 rounded-lg bg-red-500/10 flex items-center justify-center mb-4">
            <svg class="w-5 h-5 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <p class="text-[8px] font-black uppercase tracking-widest text-red-500 dark:text-red-400 mb-1">Acción irreversible</p>
        <h4 class="text-base font-black text-gray-900 dark:text-white tracking-tight mb-3">¿Eliminar comprobantes?</h4>
        <p id="vc_confirm_message" class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-2"></p>
        <p class="text-[10px] text-gray-500 dark:text-gray-400 mb-6">Los registros de cobro se mantienen; solo se borran las imágenes del servidor.</p>
        <div class="flex items-center justify-between">
            <button type="button" onclick="closeVcConfirmModal()"
                class="text-[9px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                Cancelar
            </button>
            <button type="button" onclick="submitVcCleanup()"
                class="text-[9px] font-black uppercase tracking-widest text-red-500 dark:text-red-400 border-b border-red-400 dark:border-red-500/60 pb-px hover:opacity-60 transition-opacity">
                Sí, eliminar
            </button>
        </div>
    </div>
</div>

<script>
    let _vcPreviewReady = false;

    function formatVcDate(iso) {
        if (!iso) return '—';
        return new Date(iso + 'T12:00:00').toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function syncVcDateDisplay(which) {
        const input = document.getElementById(which === 'from' ? 'vc_from_date' : 'vc_to_date');
        const display = document.getElementById(which === 'from' ? 'vc_from_display' : 'vc_to_display');
        display.textContent = formatVcDate(input.value);
    }

    function openVcDatePicker(which) {
        const input = document.getElementById(which === 'from' ? 'vc_from_date' : 'vc_to_date');
        if (input.showPicker) input.showPicker();
        else input.click();
    }

    document.querySelectorAll('.vc-date-box').forEach((box, i) => {
        box.addEventListener('click', (e) => {
            if (e.target.tagName === 'INPUT') return;
            openVcDatePicker(i === 0 ? 'from' : 'to');
        });
    });

    function openVoucherCleanupModal() {
        const to = new Date();
        const from = new Date();
        from.setMonth(from.getMonth() - 3);

        document.getElementById('vc_to_date').value = to.toISOString().slice(0, 10);
        document.getElementById('vc_from_date').value = from.toISOString().slice(0, 10);
        syncVcDateDisplay('from');
        syncVcDateDisplay('to');

        document.getElementById('vc_preview_panel').classList.add('hidden');
        document.getElementById('vc_confirm_btn').disabled = true;
        _vcPreviewReady = false;

        document.getElementById('voucherCleanupModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeVoucherCleanupModal() {
        closeVcConfirmModal();
        document.getElementById('voucherCleanupModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function previewVoucherCleanup() {
        const from = document.getElementById('vc_from_date').value;
        const to = document.getElementById('vc_to_date').value;
        const btn = document.getElementById('vc_preview_btn');

        if (!from || !to) { alert('Selecciona ambas fechas.'); return; }
        if (from > to) { alert('La fecha inicial no puede ser posterior a la final.'); return; }

        btn.disabled = true;
        btn.textContent = 'Calculando…';

        fetch('{{ route('superadmin.installments.vouchers.preview') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ from_date: from, to_date: to }),
        })
        .then(r => r.json().then(data => ({ ok: r.ok, status: r.status, data })))
        .then(({ ok, data }) => {
            if (!ok) {
                const msg = data.message || (data.errors && Object.values(data.errors).flat().join(' ')) || 'Error al calcular';
                throw new Error(msg);
            }
            renderVoucherCleanupPreview(data, from, to);
        })
        .catch(err => alert(err.message || 'No se pudo obtener la vista previa.'))
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Calcular espacio';
        });
    }

    function renderVoucherCleanupPreview(data, from, to) {
        const fromLabel = formatVcDate(from);
        const toLabel = formatVcDate(to);

        document.getElementById('vc_range_label').textContent = fromLabel + ' → ' + toLabel;
        document.getElementById('vc_size_label').textContent = data.total_human;
        document.getElementById('vc_count_label').textContent = data.count + ' archivo(s)';

        document.getElementById('vc_form_from').value = from;
        document.getElementById('vc_form_to').value = to;

        const list = document.getElementById('vc_items_list');
        const empty = document.getElementById('vc_empty_msg');
        list.innerHTML = '';

        if (!data.items || data.items.length === 0) {
            empty.classList.remove('hidden');
            document.getElementById('vc_confirm_btn').disabled = true;
            _vcPreviewReady = false;
        } else {
            empty.classList.add('hidden');
            data.items.forEach(item => {
                const li = document.createElement('li');
                const canPreview = !!item.url;
                li.className = 'px-2.5 py-2 rounded-lg border border-transparent ' + (canPreview
                    ? 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-900 hover:border-gray-100 dark:hover:border-gray-800 group'
                    : 'opacity-50 cursor-default');
                li.innerHTML = `
                    <div class="flex justify-between gap-2 items-start">
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-black text-gray-900 dark:text-white truncate">${escapeHtml(item.client)}</p>
                            <p class="text-[9px] text-gray-500 dark:text-gray-400 truncate">${escapeHtml(item.label)} · #${item.loan_id}</p>
                            <p class="text-[8px] text-gray-500 dark:text-gray-400 truncate">${escapeHtml(item.filename)}</p>
                        </div>
                        <div class="flex flex-col items-end gap-1 flex-shrink-0">
                            <span class="text-[9px] font-bold text-green-600 dark:text-green-400">${escapeHtml(item.size_human)}</span>
                            ${canPreview ? `<svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>` : ''}
                        </div>
                    </div>
                `;
                if (canPreview) {
                    li.addEventListener('click', () => {
                        if (typeof openVoucherLightbox === 'function') openVoucherLightbox(item.url);
                    });
                }
                list.appendChild(li);
            });
            document.getElementById('vc_confirm_btn').disabled = false;
            _vcPreviewReady = true;
        }

        document.getElementById('vc_preview_panel').classList.remove('hidden');
    }

    function escapeHtml(str) {
        if (!str) return '';
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function openVcConfirmModal() {
        if (!_vcPreviewReady) {
            alert('Calcula primero el espacio a liberar.');
            return;
        }
        const count = document.getElementById('vc_count_label').textContent;
        const size = document.getElementById('vc_size_label').textContent;
        const range = document.getElementById('vc_range_label').textContent;
        document.getElementById('vc_confirm_message').innerHTML =
            `Se eliminarán <span class="font-black text-gray-900 dark:text-white">${escapeHtml(count)}</span> ` +
            `(<span class="font-black text-green-600 dark:text-green-400">${escapeHtml(size)}</span>) ` +
            `del rango <span class="font-black text-gray-900 dark:text-white">${escapeHtml(range)}</span>.`;
        document.getElementById('vcConfirmModal').classList.remove('hidden');
    }

    function closeVcConfirmModal() {
        document.getElementById('vcConfirmModal').classList.add('hidden');
    }

    function submitVcCleanup() {
        document.getElementById('vc_cleanup_form').submit();
    }

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        if (!document.getElementById('vcConfirmModal').classList.contains('hidden')) {
            closeVcConfirmModal();
            return;
        }
        if (!document.getElementById('voucherCleanupModal').classList.contains('hidden')) {
            closeVoucherCleanupModal();
        }
    });
</script>
