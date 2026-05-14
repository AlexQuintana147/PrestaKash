<!-- Modal Detalle Préstamo -->
<div id="loanDetailModal"
    class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-[100] flex items-center justify-center p-4"
    onclick="if(event.target===this) closeLoanDetailModal()">
    <div class="bg-white dark:bg-gray-950 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">

        <!-- Header usuario -->
        <div class="flex items-center gap-4 px-6 pt-6 pb-5 border-b border-gray-100 dark:border-gray-900">
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

            <!-- Badges email + wssp -->
            <div class="flex flex-wrap gap-2" id="ldm_badges"></div>

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
const _emailSvg = `<svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><rect x="7.904" y="119.947" style="fill:#5EBAE7;" width="496.195" height="272.109"/><polygon style="fill:#36A9E1;" points="256,280.01 23.91,127.951 7.904,135.954 256,312.022 352.037,223.987 "/><path style="fill:#1D1D1B;" d="M0,112.043v287.913h512V112.043H0z M477.266,127.851L256,270.603L34.733,127.851H477.266z M15.807,384.149V134.452L256,289.414l240.193-154.962v249.696H15.807V384.149z"/><g><rect x="215.982" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807"/><rect x="247.997" y="344.138" style="fill:#FFFFFF;" width="16.006" height="15.807"/><rect x="280.013" y="344.138" style="fill:#FFFFFF;" width="16.007" height="15.807"/></g></svg>`;

const _wsspSvg = `<svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M28 16C28 22.6274 22.6274 28 16 28C13.4722 28 11.1269 27.2184 9.19266 25.8837L5.09091 26.9091L6.16576 22.8784C4.80092 20.9307 4 18.5589 4 16C4 9.37258 9.37258 4 16 4C22.6274 4 28 9.37258 28 16Z" fill="url(#wldm)"/><path fill-rule="evenodd" clip-rule="evenodd" d="M16 30C23.732 30 30 23.732 30 16C30 8.26801 23.732 2 16 2C8.26801 2 2 8.26801 2 16C2 18.5109 2.661 20.8674 3.81847 22.905L2 30L9.31486 28.3038C11.1894 29.3854 13.5789 30 16 30ZM16 27.8462C22.5425 27.8462 27.8462 22.5425 27.8462 16C27.8462 9.45755 22.5425 4.15385 16 4.15385C9.45755 4.15385 4.15385 9.45755 4.15385 16C4.15385 18.5261 4.9445 20.8675 6.29184 22.7902L5.23077 26.7692L9.27993 25.7569C11.1894 27.0746 13.5046 27.8462 16 27.8462Z" fill="white"/><path d="M12.5 9.49989C12.1672 8.83131 11.6565 8.8905 11.1407 8.8905C10.2188 8.8905 8.78125 9.99478 8.78125 12.05C8.78125 13.7343 9.52345 15.578 12.0244 18.3361C14.438 20.9979 17.6094 22.3748 20.2422 22.3279C22.875 22.2811 23.4167 20.0154 23.4167 19.2503C23.4167 18.9112 23.2062 18.742 23.0613 18.696C22.1641 18.2654 20.5093 17.4631 20.1328 17.3124C19.7563 17.1617 19.5597 17.3656 19.4375 17.4765C19.0961 17.8018 18.4193 18.7608 18.1875 18.9765C17.9558 19.1922 17.6103 19.083 17.4665 19.0015C16.9374 18.7892 15.5029 18.1511 14.3595 17.0426C12.9453 15.6718 12.8623 15.2001 12.5959 14.7803C12.3828 14.4444 12.5392 14.2384 12.6172 14.1483C12.9219 13.7968 13.3426 13.254 13.5313 12.9843C13.7199 12.7145 13.5702 12.305 13.4803 12.05C13.0938 10.953 12.7663 10.0347 12.5 9.49989Z" fill="white"/><defs><linearGradient id="wldm" x1="26.5" y1="7" x2="4" y2="28" gradientUnits="userSpaceOnUse"><stop stop-color="#5BD066"/><stop offset="1" stop-color="#27B43E"/></linearGradient></defs></svg>`;

function openLoanDetailModal(data) {
    document.getElementById('ldm_photo').src = data.photo;
    document.getElementById('ldm_name').textContent = data.name;
    document.getElementById('ldm_meta').textContent = 'Préstamo #' + data.loanId + ' · ' + data.status;
    document.getElementById('ldm_profile_link').href = data.profileUrl;

    // Badges email + wssp
    let badges = '';
    if (data.email) {
        badges += `<a href="mailto:${data.email}" class="flex items-center gap-1 bg-blue-500 hover:bg-blue-600 transition-colors px-2 py-1 text-white font-bold text-[9px]">${data.email} ${_emailSvg}</a>`;
    }
    if (data.phone) {
        const clean = data.phone.replace(/\D/g, '');
        badges += `<a href="https://wa.me/51${clean}" target="_blank" class="flex items-center gap-1 bg-green-500 hover:bg-green-600 transition-colors px-2 py-1 text-white font-bold text-[9px]">${data.phone} ${_wsspSvg}</a>`;
    }
    document.getElementById('ldm_badges').innerHTML = badges;

    // Datos personales
    const personalRows = [
        ['DNI',       data.dni],
        ['RUC',       data.ruc],
        ['Razón Social', data.businessName],
        ['Dirección', data.address],
    ];
    document.getElementById('ldm_personal').innerHTML = personalRows
        .filter(([, v]) => v)
        .map(([label, value]) => `
            <div class="flex justify-between items-start gap-4 border-b border-gray-50 dark:border-gray-900 pb-2">
                <span class="text-[10px] text-gray-400 flex-shrink-0">${label}</span>
                <span class="text-[10px] font-bold text-gray-900 dark:text-white text-right">${value}</span>
            </div>`).join('');

    // Detalles del préstamo
    const loanRows = [
        ['Monto',      data.amount],
        ['Total',      data.total],
        ['Interés',    data.rate],
        ['Cuotas',     data.installmentsCount],
        ['Frecuencia', data.frequency],
        ['Sistema',    data.system],
        ['Banco',      data.bank],
        ['N° Cuenta',  data.account],
        ['Motivo',     data.reason],
    ];
    document.getElementById('ldm_loan').innerHTML = loanRows
        .filter(([, v]) => v)
        .map(([label, value]) => `
            <div class="flex justify-between items-start gap-4 border-b border-gray-50 dark:border-gray-900 pb-2">
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
