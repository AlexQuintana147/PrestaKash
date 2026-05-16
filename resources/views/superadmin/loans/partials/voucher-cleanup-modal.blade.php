<!-- Modal Limpiar Comprobantes -->
<div id="voucherCleanupModal"
    class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-[120] flex items-center justify-center p-4"
    onclick="if(event.target===this) closeVoucherCleanupModal()">
    <div class="bg-white dark:bg-gray-950 w-full max-w-lg rounded-2xl shadow-2xl flex flex-col max-h-[90vh]"
        onclick="event.stopPropagation()">

        <div class="relative p-6 border-b border-gray-100 dark:border-gray-900 flex-shrink-0">
            <button type="button" onclick="closeVoucherCleanupModal()"
                class="absolute top-5 right-5 text-gray-300 dark:text-gray-600 hover:text-gray-900 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <p class="text-[8px] font-black uppercase tracking-widest text-green-600 mb-1">Mantenimiento</p>
            <h3 class="text-lg font-black text-gray-900 dark:text-white tracking-tight">Limpiar comprobantes</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">
                Vista previa por rango de fechas de pago. Los archivos se borran del servidor; los registros de cobro se mantienen.
            </p>
        </div>

        <div class="p-6 space-y-4 flex-shrink-0">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-2">Desde</label>
                    <input type="date" id="vc_from_date"
                        class="w-full bg-gray-50 dark:bg-gray-900 rounded-xl px-3 py-2.5 text-sm font-bold text-gray-900 dark:text-white border border-gray-100 dark:border-gray-800 focus:border-green-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-2">Hasta</label>
                    <input type="date" id="vc_to_date"
                        class="w-full bg-gray-50 dark:bg-gray-900 rounded-xl px-3 py-2.5 text-sm font-bold text-gray-900 dark:text-white border border-gray-100 dark:border-gray-800 focus:border-green-500 focus:outline-none">
                </div>
            </div>
            <button type="button" id="vc_preview_btn" onclick="previewVoucherCleanup()"
                class="w-full py-3 rounded-xl bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white text-[10px] font-black uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors">
                Calcular espacio a liberar
            </button>
        </div>

        <div id="vc_preview_panel" class="hidden flex-1 flex flex-col min-h-0 border-t border-gray-100 dark:border-gray-900">
            <div class="px-6 py-4 bg-green-50 dark:bg-green-500/5 border-b border-green-100 dark:border-green-500/20 flex-shrink-0">
                <p class="text-[9px] font-black uppercase tracking-widest text-green-600 mb-2">Resumen</p>
                <div class="grid grid-cols-2 gap-3 text-[10px]">
                    <div>
                        <span class="text-gray-500 uppercase font-bold tracking-widest block">Rango</span>
                        <span id="vc_range_label" class="font-black text-gray-900 dark:text-white"></span>
                    </div>
                    <div>
                        <span class="text-gray-500 uppercase font-bold tracking-widest block">Espacio</span>
                        <span id="vc_size_label" class="font-black text-green-600"></span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-500 uppercase font-bold tracking-widest block">Comprobantes</span>
                        <span id="vc_count_label" class="font-black text-gray-900 dark:text-white"></span>
                    </div>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto px-6 py-3 min-h-0" style="max-height: 220px;">
                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2 sticky top-0 bg-white dark:bg-gray-950 py-1">
                    Se eliminarán
                </p>
                <ul id="vc_items_list" class="space-y-2"></ul>
                <p id="vc_empty_msg" class="hidden text-xs text-gray-400 text-center py-6">No hay comprobantes en este rango.</p>
            </div>
        </div>

        <div class="p-6 border-t border-gray-100 dark:border-gray-900 flex items-center gap-4 flex-shrink-0">
            <button type="button" onclick="closeVoucherCleanupModal()"
                class="text-[9px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                Cancelar
            </button>
            <form id="vc_cleanup_form" method="POST" action="{{ route('superadmin.installments.vouchers.cleanup') }}" class="ml-auto">
                @csrf
                <input type="hidden" name="from_date" id="vc_form_from">
                <input type="hidden" name="to_date" id="vc_form_to">
                <button type="submit" id="vc_confirm_btn" disabled
                    class="text-[9px] font-black uppercase tracking-widest text-red-500 border-b border-red-400 pb-px hover:opacity-60 transition-opacity disabled:opacity-30 disabled:cursor-not-allowed disabled:border-gray-300 disabled:text-gray-400">
                    Confirmar limpieza
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    let _vcPreviewReady = false;

    function openVoucherCleanupModal() {
        const modal = document.getElementById('voucherCleanupModal');
        const to = new Date();
        const from = new Date();
        from.setMonth(from.getMonth() - 3);

        document.getElementById('vc_to_date').value = to.toISOString().slice(0, 10);
        document.getElementById('vc_from_date').value = from.toISOString().slice(0, 10);

        document.getElementById('vc_preview_panel').classList.add('hidden');
        document.getElementById('vc_confirm_btn').disabled = true;
        _vcPreviewReady = false;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeVoucherCleanupModal() {
        document.getElementById('voucherCleanupModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function previewVoucherCleanup() {
        const from = document.getElementById('vc_from_date').value;
        const to = document.getElementById('vc_to_date').value;
        const btn = document.getElementById('vc_preview_btn');

        if (!from || !to) {
            alert('Selecciona ambas fechas.');
            return;
        }
        if (from > to) {
            alert('La fecha inicial no puede ser posterior a la final.');
            return;
        }

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
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
            if (!ok) throw new Error(data.message || 'Error al calcular');
            renderVoucherCleanupPreview(data, from, to);
        })
        .catch(err => alert(err.message || 'No se pudo obtener la vista previa.'))
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Calcular espacio a liberar';
        });
    }

    function renderVoucherCleanupPreview(data, from, to) {
        const fromLabel = new Date(from + 'T12:00:00').toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' });
        const toLabel = new Date(to + 'T12:00:00').toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' });

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
                li.className = 'p-2.5 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800';
                li.innerHTML = `
                    <div class="flex justify-between gap-2 text-[10px]">
                        <span class="font-black text-gray-900 dark:text-white truncate">${escapeHtml(item.client)}</span>
                        <span class="text-green-600 font-bold flex-shrink-0">${escapeHtml(item.size_human)}</span>
                    </div>
                    <p class="text-[9px] text-gray-500 mt-0.5">${escapeHtml(item.label)} · Préstamo #${item.loan_id}</p>
                    <p class="text-[8px] text-gray-400 mt-0.5 truncate">${escapeHtml(item.filename)} · ${escapeHtml(item.paid_at_label || '')}</p>
                `;
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

    document.getElementById('vc_cleanup_form').addEventListener('submit', function (e) {
        if (!_vcPreviewReady) {
            e.preventDefault();
            alert('Calcula primero el espacio a liberar.');
            return;
        }
        const count = document.getElementById('vc_count_label').textContent;
        const size = document.getElementById('vc_size_label').textContent;
        const range = document.getElementById('vc_range_label').textContent;
        if (!confirm(`¿Eliminar ${count} (${size}) del rango ${range}? Esta acción no se puede deshacer.`)) {
            e.preventDefault();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !document.getElementById('voucherCleanupModal').classList.contains('hidden')) {
            closeVoucherCleanupModal();
        }
    });
</script>
