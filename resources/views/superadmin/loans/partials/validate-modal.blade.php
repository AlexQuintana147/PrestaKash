<!-- Modal Validación de Pago -->
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
            <div class="flex-1 flex items-center justify-center p-3 cursor-zoom-in" style="min-height: 200px;"
                 onclick="openVoucherLightbox()">
                <img id="modal_voucher_img" src="" alt="Comprobante"
                    class="max-w-full max-h-full object-contain rounded-lg"
                    draggable="false">
            </div>
            <p class="text-[8px] text-center text-gray-300 dark:text-gray-700 py-2 font-medium">Click para ampliar</p>
        </div>

        <!-- Acciones (Derecha) -->
        <div class="w-full md:w-1/2 p-6 flex flex-col relative overflow-y-auto">
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

<!-- Lightbox fullscreen -->
<div id="voucherLightbox"
    class="hidden fixed inset-0 z-[200] bg-black/95 flex items-center justify-center select-none"
    onclick="closeLightboxOnBackdrop(event)">

    <!-- Cerrar -->
    <button onclick="closeVoucherLightbox()"
        class="absolute top-5 right-5 text-white/50 hover:text-white transition-colors z-10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <!-- Hint -->
    <p id="lightbox_hint" class="absolute bottom-5 left-1/2 -translate-x-1/2 text-[9px] font-black uppercase tracking-widest text-white/30 pointer-events-none transition-opacity duration-500">
        Scroll para zoom · Arrastra para mover
    </p>

    <!-- Contenedor de imagen -->
    <div id="lightbox_container" class="w-full h-full overflow-hidden cursor-zoom-in">
        <img id="lightbox_img" src="" alt="Comprobante"
            class="w-full h-full object-contain pointer-events-none"
            style="transform-origin: center center; transform: scale(1) translate(0px, 0px);"
            draggable="false">
    </div>
</div>

<script>
(function () {
    let scale = 1, tx = 0, ty = 0;
    let dragging = false, moved = false, sx = 0, sy = 0;

    function applyTransform() {
        document.getElementById('lightbox_img').style.transform =
            `scale(${scale}) translate(${tx}px, ${ty}px)`;
    }

    function resetLightbox() {
        scale = 1; tx = 0; ty = 0;
        applyTransform();
        const c = document.getElementById('lightbox_container');
        c.style.cursor = 'zoom-in';
    }

    // Scroll → zoom
    document.getElementById('lightbox_container').addEventListener('wheel', function (e) {
        e.preventDefault();
        scale = Math.min(8, Math.max(1, scale * (1 - e.deltaY * 0.001)));
        if (scale <= 1) { scale = 1; tx = 0; ty = 0; this.style.cursor = 'zoom-in'; }
        else this.style.cursor = 'grab';
        applyTransform();
    }, { passive: false });

    // Click → toggle zoom rápido
    document.getElementById('lightbox_container').addEventListener('click', function () {
        if (moved) return;
        if (scale === 1) { scale = 2.5; this.style.cursor = 'grab'; }
        else { resetLightbox(); }
        applyTransform();
    });

    // Drag
    document.getElementById('lightbox_container').addEventListener('mousedown', function (e) {
        if (scale === 1) return;
        dragging = true; moved = false;
        sx = e.clientX - tx * scale;
        sy = e.clientY - ty * scale;
        this.style.cursor = 'grabbing';
    });

    document.addEventListener('mousemove', function (e) {
        if (!dragging) return;
        moved = true;
        tx = (e.clientX - sx) / scale;
        ty = (e.clientY - sy) / scale;
        applyTransform();
    });

    document.addEventListener('mouseup', function () {
        if (!dragging) return;
        dragging = false;
        const c = document.getElementById('lightbox_container');
        c.style.cursor = scale > 1 ? 'grab' : 'zoom-in';
        setTimeout(() => { moved = false; }, 50);
    });

    window.openVoucherLightbox = function () {
        const src = document.getElementById('modal_voucher_img').src;
        document.getElementById('lightbox_img').src = src;
        resetLightbox();
        document.getElementById('voucherLightbox').classList.remove('hidden');
        // Ocultar hint después de 2s
        setTimeout(() => {
            const h = document.getElementById('lightbox_hint');
            if (h) h.style.opacity = '0';
        }, 2000);
    };

    window.closeVoucherLightbox = function () {
        document.getElementById('voucherLightbox').classList.add('hidden');
        document.getElementById('lightbox_hint').style.opacity = '1';
        resetLightbox();
    };

    window.closeLightboxOnBackdrop = function (e) {
        if (e.target === document.getElementById('voucherLightbox') ||
            e.target === document.getElementById('lightbox_container')) {
            if (!moved) closeVoucherLightbox();
        }
    };

    // Esc cierra lightbox
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (!document.getElementById('voucherLightbox').classList.contains('hidden')) {
                closeVoucherLightbox();
            }
        }
    });
})();
</script>
