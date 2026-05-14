<div id="validateModal"
    class="hidden fixed inset-0 bg-black/80 backdrop-blur-xl z-[100] flex items-center justify-center p-4">
    <div
        class="bg-white dark:bg-gray-950 w-full max-w-5xl max-h-[90vh] rounded-[3rem] shadow-2xl relative flex flex-col md:flex-row overflow-hidden">

        <!-- Voucher (Izquierda) -->
        <div class="w-full md:w-3/5 bg-gray-50 dark:bg-gray-900 p-6 md:p-10 flex flex-col min-h-0">
            <div class="flex items-center justify-between mb-6">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Comprobante de Pago</p>
                <a id="modal_voucher_link" href="" target="_blank"
                    class="p-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 text-gray-500 hover:text-purple-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
            <div class="flex-1 min-h-0 rounded-[2rem] overflow-hidden border border-gray-100 dark:border-gray-800 bg-white dark:bg-black shadow-inner">
                <img id="modal_voucher_img" src="" class="w-full h-full object-contain">
            </div>
        </div>

        <!-- Acciones (Derecha) -->
        <div class="w-full md:w-2/5 p-8 md:p-12 flex flex-col relative">
            <button onclick="closeValidateModal()"
                class="absolute top-8 right-8 w-10 h-10 rounded-full bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="mb-12">
                <span class="px-3 py-1 bg-purple-100 text-purple-600 dark:bg-purple-900/20 dark:text-purple-400 text-[8px] font-black uppercase tracking-[0.2em] rounded-full mb-4 inline-block">Proceso de Validación</span>
                <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight mb-4">Revisar Pago</h3>
                <div id="modal_info_text" class="space-y-2"></div>
            </div>

            <div class="mt-auto space-y-6">
                <!-- Rechazar -->
                <form id="rejectForm" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-3">Motivo del rechazo</label>
                        <textarea name="reason" required placeholder="Ej: Monto incorrecto o imagen borrosa..."
                            class="w-full bg-gray-50 dark:bg-gray-900 rounded-2xl p-4 text-sm border-2 border-transparent focus:border-red-500 focus:bg-white dark:focus:bg-gray-950 transition-all resize-none h-24 outline-none"></textarea>
                    </div>
                    <button type="submit"
                        class="w-full py-4 text-[10px] font-black uppercase tracking-[0.2em] text-red-500 bg-red-50 dark:bg-red-900/10 rounded-2xl hover:bg-red-100 dark:hover:bg-red-900/20 transition-all">
                        Rechazar y Notificar
                    </button>
                </form>

                <div class="relative flex items-center gap-4">
                    <div class="flex-1 h-px bg-gray-100 dark:bg-gray-900"></div>
                    <span class="text-[10px] font-black text-gray-300 uppercase">o</span>
                    <div class="flex-1 h-px bg-gray-100 dark:bg-gray-900"></div>
                </div>

                <!-- Aprobar -->
                <form id="approveForm" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full py-5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all shadow-xl shadow-gray-900/20 dark:shadow-none">
                        Confirmar Aprobación
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
