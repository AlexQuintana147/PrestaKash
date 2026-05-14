<!-- Modal Confirmar Reversión -->
<div id="unapproveModal"
    class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-[100] flex items-center justify-center p-4"
    onclick="if(event.target===this) closeUnapproveModal()">
    <div class="bg-white dark:bg-gray-950 w-full max-w-sm rounded-2xl shadow-2xl p-6">

        <!-- Icono -->
        <div class="w-10 h-10 rounded-full bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center mb-4">
            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        </div>

        <p class="text-[8px] font-black uppercase tracking-widest text-orange-500 mb-1">Acción irreversible</p>
        <h3 class="text-base font-black text-gray-900 dark:text-white tracking-tight mb-2">Revertir pago</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
            El pago volverá al estado <span class="font-bold text-gray-900 dark:text-white">En revisión</span>.
            El cliente deberá volver a subir su comprobante si es necesario.
        </p>

        <div class="flex items-center gap-4">
            <button onclick="closeUnapproveModal()"
                class="text-[9px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                Cancelar
            </button>
            <form id="unapproveForm" method="POST" class="ml-auto">
                @csrf
                <button type="submit"
                    class="text-[9px] font-black uppercase tracking-widest text-orange-500 border-b border-orange-400 pb-px hover:opacity-60 transition-opacity">
                    Sí, revertir pago
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openUnapproveModal(action) {
    document.getElementById('unapproveForm').action = action;
    document.getElementById('unapproveModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeUnapproveModal() {
    document.getElementById('unapproveModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('unapproveModal').classList.contains('hidden')) {
        closeUnapproveModal();
    }
});
</script>
