<!-- Modal Detalle Préstamo -->
<div id="loanDetailModal"
    class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-[100] flex items-center justify-center p-4"
    onclick="if(event.target===this) closeLoanDetailModal()">
    <div class="bg-white dark:bg-gray-950 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">

        <!-- Header usuario -->
        <div id="ldm_header" class="flex items-center gap-4 px-6 pt-6 pb-5 border-b border-gray-100 dark:border-gray-900">
            <div class="w-11 h-11 rounded-xl bg-gray-100 dark:bg-gray-900 overflow-hidden flex-shrink-0 border border-gray-100 dark:border-gray-800">
                <img id="ldm_photo" src="" class="w-full h-full object-cover">
            </div>
            <div class="flex-1 min-w-0">
                <p id="ldm_name" class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight truncate"></p>
                <p id="ldm_meta" class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-0.5"></p>
            </div>
            <button onclick="closeLoanDetailModal()" class="text-gray-300 dark:text-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="px-6 py-5 space-y-6 overflow-y-auto" style="max-height: 70vh;">

            <!-- Datos personales -->
            <div>
                <p class="text-[8px] font-black uppercase tracking-widest text-gray-400 mb-3">Datos personales</p>
                <div class="space-y-2.5" id="ldm_personal"></div>
            </div>

            <!-- Detalles del préstamo -->
            <div>
                <p class="text-[8px] font-black uppercase tracking-widest text-gray-400 mb-3">Préstamo</p>
                <div class="space-y-2.5" id="ldm_loan"></div>
            </div>

            <!-- Enlace al perfil -->
            <a id="ldm_profile_link" href="#"
                class="flex items-center justify-between w-full pt-4 border-t border-gray-100 dark:border-gray-900 text-[9px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors group">
                Ver perfil completo
                <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</div>

<script>
function openLoanDetailModal(data) {
    // Header
    document.getElementById('ldm_photo').src = data.photo;
    document.getElementById('ldm_name').textContent = data.name;
    document.getElementById('ldm_meta').textContent = 'Préstamo #' + data.loanId + ' · ' + data.status;
    document.getElementById('ldm_profile_link').href = data.profileUrl;

    // Datos personales
    const personalRows = [
        ['DNI',       data.dni],
        ['Teléfono',  data.phone],
        ['Correo',    data.email],
        ['Dirección', data.address],
    ];
    document.getElementById('ldm_personal').innerHTML = personalRows
        .filter(([, v]) => v)
        .map(([label, value]) => `
            <div class="flex justify-between items-start gap-4">
                <span class="text-[10px] text-gray-400 flex-shrink-0">${label}</span>
                <span class="text-[10px] font-bold text-gray-900 dark:text-white text-right">${value}</span>
            </div>`).join('');

    // Detalles del préstamo
    const loanRows = [
        ['Monto',       data.amount],
        ['Total',       data.total],
        ['Interés',     data.rate],
        ['Cuotas',      data.installmentsCount],
        ['Frecuencia',  data.frequency],
        ['Sistema',     data.system],
        ['Banco',       data.bank],
        ['N° Cuenta',   data.account],
        ['Motivo',      data.reason],
    ];
    document.getElementById('ldm_loan').innerHTML = loanRows
        .filter(([, v]) => v)
        .map(([label, value]) => `
            <div class="flex justify-between items-start gap-4">
                <span class="text-[10px] text-gray-400 flex-shrink-0">${label}</span>
                <span class="text-[10px] font-bold text-gray-900 dark:text-white text-right max-w-[60%]">${value}</span>
            </div>`).join('');

    document.getElementById('loanDetailModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeLoanDetailModal() {
    document.getElementById('loanDetailModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('loanDetailModal').classList.contains('hidden')) {
        closeLoanDetailModal();
    }
});
</script>
