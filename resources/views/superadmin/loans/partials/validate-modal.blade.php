<!-- Modal Validación de Pago -->
<style>
#vm_actions::-webkit-scrollbar { width: 3px; }
#vm_actions::-webkit-scrollbar-track { background: transparent; }
#vm_actions::-webkit-scrollbar-thumb { background: rgba(156,163,175,0.35); border-radius: 9999px; }
#vm_actions::-webkit-scrollbar-thumb:hover { background: rgba(156,163,175,0.6); }
.dark #vm_actions::-webkit-scrollbar-thumb { background: rgba(75,85,99,0.5); }
.dark #vm_actions::-webkit-scrollbar-thumb:hover { background: rgba(75,85,99,0.8); }
</style>
<div id="validateModal"
    class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-950 w-full max-w-2xl rounded-2xl shadow-2xl relative flex flex-col md:flex-row overflow-hidden"
         style="max-height: 90vh;">

        <!-- Voucher (Izquierda) -->
        <div class="w-full md:w-1/2 bg-gray-50 dark:bg-gray-900 flex flex-col" style="min-height: 260px;">
            <div class="flex items-center justify-between px-4 pt-4 pb-2">
                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Comprobante</p>
                <a id="modal_voucher_link" href="" target="_blank"
                    class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                </a>
            </div>
            <!-- Imagen — click abre lightbox -->
            <div class="flex-1 flex items-center justify-center p-3 cursor-zoom-in group relative" style="min-height: 200px;"
                 onclick="openVoucherLightbox()">
                <img id="modal_voucher_img" src="" alt="Comprobante"
                    class="max-w-full max-h-full object-contain rounded-lg group-hover:opacity-80 transition-opacity"
                    draggable="false">
                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                    <svg class="w-6 h-6 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                    </svg>
                </div>
            </div>
            <p class="text-[8px] text-center text-gray-300 dark:text-gray-700 py-2 font-medium">Click para ampliar</p>
        </div>

        <!-- Acciones (Derecha) -->
        <div id="vm_actions" class="w-full md:w-1/2 p-6 flex flex-col relative overflow-y-auto modal-scroll" style="scrollbar-width: thin; scrollbar-color: rgba(156,163,175,0.4) transparent;">
            <button onclick="closeValidateModal()"
                class="absolute top-4 right-4 text-gray-300 dark:text-gray-600 hover:text-gray-900 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="mb-6 pr-6">
                <p class="text-[8px] font-black uppercase tracking-widest text-purple-500 mb-2">Validación</p>
                <h3 class="text-lg font-black text-gray-900 dark:text-white tracking-tight mb-4">Revisar Pago</h3>
                <div id="modal_info_text" class="space-y-1.5"></div>
            </div>

            <div class="mt-auto space-y-4">
                <form id="rejectForm" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-2">Motivo del rechazo</label>
                        <textarea name="reason" required placeholder="Ej: Monto incorrecto o imagen borrosa..."
                            class="w-full bg-gray-50 dark:bg-gray-900 rounded-xl p-3 text-xs border border-gray-100 dark:border-gray-800 focus:border-red-400 focus:outline-none transition-colors resize-none h-20"></textarea>
                    </div>
                    <button type="submit"
                        class="text-[9px] font-black uppercase tracking-widest text-red-500 border-b border-red-400 pb-px hover:opacity-60 transition-opacity">
                        Rechazar y Notificar
                    </button>
                </form>

                <div class="flex items-center gap-3">
                    <div class="flex-1 h-px bg-gray-100 dark:bg-gray-900"></div>
                    <span class="text-[9px] font-black text-gray-300 dark:text-gray-700 uppercase">o</span>
                    <div class="flex-1 h-px bg-gray-100 dark:bg-gray-900"></div>
                </div>

                <form id="approveForm" method="POST">
                    @csrf
                    <button type="submit"
                        class="text-[9px] font-black uppercase tracking-widest text-gray-900 dark:text-white border-b border-gray-900 dark:border-white pb-px hover:opacity-60 transition-opacity">
                        Confirmar Aprobación
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox fullscreen (mismo diseño que /usuarios/[ID]) -->
<div id="voucherLightbox" onclick="closeLightboxBackdrop(event)"
     class="hidden fixed inset-0 z-[200] bg-black/90 flex flex-col items-center justify-center p-4">

    <!-- Header -->
    <div class="flex items-center justify-between w-full max-w-4xl mb-3 flex-shrink-0">
        <p class="text-sm text-gray-400">Comprobante de Pago</p>
        <div class="flex items-center gap-3">
            <!-- Zoom out -->
            <button onclick="voucherZoom(-0.25)" class="p-2 text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/>
                </svg>
            </button>
            <!-- Zoom level -->
            <span id="voucherZoomLevel" class="text-xs text-gray-500 w-10 text-center">100%</span>
            <!-- Zoom in -->
            <button onclick="voucherZoom(0.25)" class="p-2 text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                </svg>
            </button>
            <!-- Reset -->
            <button onclick="voucherResetZoom()" class="p-2 text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
            <!-- Cerrar -->
            <button onclick="closeVoucherLightbox()" class="p-2 text-gray-400 hover:text-white transition-colors ml-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Imagen -->
    <div class="overflow-hidden w-full max-w-4xl flex items-center justify-center" style="max-height: 80vh;">
        <img id="voucherLightboxImg" src="" alt="Comprobante"
             class="transition-transform duration-200 rounded-lg select-none"
             style="transform-origin: center; max-width: 100%;">
    </div>
</div>

<script>
let voucherScale = 1;

function openVoucherLightbox() {
    voucherScale = 1;
    const src = document.getElementById('modal_voucher_img').src;
    document.getElementById('voucherLightboxImg').src = src;
    document.getElementById('voucherLightboxImg').style.transform = 'scale(1)';
    document.getElementById('voucherZoomLevel').textContent = '100%';
    document.getElementById('voucherLightbox').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeVoucherLightbox() {
    document.getElementById('voucherLightbox').classList.add('hidden');
    document.body.style.overflow = 'hidden'; // mantener el modal de validación bloqueado
}

function closeLightboxBackdrop(e) {
    if (e.target === document.getElementById('voucherLightbox')) closeVoucherLightbox();
}

function voucherZoom(delta) {
    voucherScale = Math.min(4, Math.max(0.25, voucherScale + delta));
    document.getElementById('voucherLightboxImg').style.transform = `scale(${voucherScale})`;
    document.getElementById('voucherZoomLevel').textContent = Math.round(voucherScale * 100) + '%';
}

function voucherResetZoom() {
    voucherScale = 1;
    document.getElementById('voucherLightboxImg').style.transform = 'scale(1)';
    document.getElementById('voucherZoomLevel').textContent = '100%';
}

// Scroll para zoom en el lightbox
document.getElementById('voucherLightbox').addEventListener('wheel', function(e) {
    if (this.classList.contains('hidden')) return;
    e.preventDefault();
    voucherZoom(e.deltaY < 0 ? 0.15 : -0.15);
}, { passive: false });

// Esc cierra el lightbox (no el modal de validación)
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('voucherLightbox').classList.contains('hidden')) {
        closeVoucherLightbox();
    }
    if (!document.getElementById('voucherLightbox').classList.contains('hidden')) {
        if (e.key === '+' || e.key === '=') voucherZoom(0.25);
        if (e.key === '-') voucherZoom(-0.25);
    }
});
</script>
