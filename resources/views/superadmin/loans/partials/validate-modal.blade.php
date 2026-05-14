<!-- Modal Validación de Pago -->
<div id="validateModal"
    class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-950 w-full max-w-2xl rounded-2xl shadow-2xl relative flex flex-col md:flex-row overflow-hidden"
         style="max-height: 90vh;">

        <!-- Voucher (Izquierda) -->
        <div class="w-full md:w-1/2 bg-gray-50 dark:bg-gray-900 flex flex-col" style="min-height: 260px;">
            <!-- Header imagen -->
            <div class="flex items-center justify-between px-4 pt-4 pb-2">
                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Comprobante</p>
                <a id="modal_voucher_link" href="" target="_blank"
                    class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
            <!-- Imagen con zoom -->
            <div id="voucher_zoom_container"
                class="flex-1 overflow-hidden relative cursor-zoom-in select-none"
                style="min-height: 200px;">
                <img id="modal_voucher_img" src="" alt="Comprobante"
                    class="w-full h-full object-contain transition-none"
                    style="transform-origin: center center; transform: scale(1) translate(0px, 0px);"
                    draggable="false">
            </div>
            <p class="text-[8px] text-center text-gray-300 dark:text-gray-700 py-2 font-medium">Click para zoom · Scroll para escalar</p>
        </div>

        <!-- Acciones (Derecha) -->
        <div class="w-full md:w-1/2 p-6 flex flex-col relative overflow-y-auto">
            <!-- Cerrar -->
            <button onclick="closeValidateModal()"
                class="absolute top-4 right-4 text-gray-300 dark:text-gray-600 hover:text-gray-900 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Info -->
            <div class="mb-6 pr-6">
                <p class="text-[8px] font-black uppercase tracking-widest text-purple-500 mb-2">Validación</p>
                <h3 class="text-lg font-black text-gray-900 dark:text-white tracking-tight mb-4">Revisar Pago</h3>
                <div id="modal_info_text" class="space-y-1.5"></div>
            </div>

            <div class="mt-auto space-y-4">
                <!-- Rechazar -->
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

                <!-- Aprobar -->
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

<script>
(function () {
    let scale = 1, originX = 0, originY = 0;
    let isDragging = false, startX = 0, startY = 0, lastX = 0, lastY = 0;

    function applyTransform() {
        const img = document.getElementById('modal_voucher_img');
        img.style.transform = `scale(${scale}) translate(${originX}px, ${originY}px)`;
    }

    function resetZoom() {
        scale = 1; originX = 0; originY = 0;
        applyTransform();
        const c = document.getElementById('voucher_zoom_container');
        c.style.cursor = 'zoom-in';
    }

    // Click para toggle zoom
    document.getElementById('voucher_zoom_container').addEventListener('click', function (e) {
        if (isDragging) return;
        if (scale === 1) {
            scale = 2.5;
            this.style.cursor = 'grab';
        } else {
            resetZoom();
        }
        applyTransform();
    });

    // Scroll para escalar
    document.getElementById('voucher_zoom_container').addEventListener('wheel', function (e) {
        e.preventDefault();
        scale = Math.min(5, Math.max(1, scale - e.deltaY * 0.005));
        if (scale === 1) { originX = 0; originY = 0; this.style.cursor = 'zoom-in'; }
        else this.style.cursor = 'grab';
        applyTransform();
    }, { passive: false });

    // Drag para mover
    document.getElementById('voucher_zoom_container').addEventListener('mousedown', function (e) {
        if (scale === 1) return;
        isDragging = false;
        startX = e.clientX - lastX;
        startY = e.clientY - lastY;
        this.style.cursor = 'grabbing';

        const onMove = (ev) => {
            isDragging = true;
            lastX = ev.clientX - startX;
            lastY = ev.clientY - startY;
            originX = lastX / scale;
            originY = lastY / scale;
            applyTransform();
        };
        const onUp = () => {
            document.removeEventListener('mousemove', onMove);
            document.removeEventListener('mouseup', onUp);
            document.getElementById('voucher_zoom_container').style.cursor = 'grab';
            setTimeout(() => { isDragging = false; }, 50);
        };
        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
    });

    // Reset al abrir
    window._resetVoucherZoom = resetZoom;
})();
</script>
