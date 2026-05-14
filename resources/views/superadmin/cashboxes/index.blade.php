@extends('superadmin.layout')

@section('title', 'Gestión de Cajas')

@section('content')
    <div class="px-6 md:px-12 py-10 max-w-[1600px] mx-auto">

        <!-- Header: Refined Typography & Alignment -->
        <div class="flex items-end justify-between mb-4">
            <div>
                <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em] mb-1">Panel
                    Administrativo</p>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Gestión de Cajas
                </h1>
            </div>
            <button onclick="openCashboxModal()"
                class="text-[10px] font-black uppercase tracking-widest text-gray-900 dark:text-white border-b border-gray-900 dark:border-white pb-1 hover:opacity-60 transition-opacity">
                Nueva Caja
            </button>
        </div>

        @if(session('success'))
            <div
                class="mb-12 flex items-center gap-3 text-xs font-bold text-green-500 uppercase tracking-widest animate-in fade-in slide-in-from-top-2 duration-500">
                <div class="w-1 h-1 rounded-full bg-green-500"></div>
                {{ session('success') }}
            </div>
        @endif

        <!-- Main Workspace: Card + Movements -->
        <div class="space-y-12">

            <!-- Carousel Section -->
            <div class="relative group/carousel">
                <!-- Navigation: Floating Glassmorphism -->
                <div
                    class="absolute inset-y-0 left-0 right-0 pointer-events-none z-20 flex justify-between items-center px-4 md:px-10">
                    <button id="prevBtn" onclick="scrollCarousel(-1)"
                        class="pointer-events-auto w-14 h-14 flex items-center justify-center rounded-full bg-white/5 dark:bg-black/5 backdrop-blur-xl border border-gray-100/50 dark:border-white/5 text-gray-400 hover:text-gray-900 dark:hover:text-white transition-all opacity-0 group-hover/carousel:opacity-100 -translate-x-4 group-hover/carousel:translate-x-0 shadow-sm disabled:hidden">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                    <button id="nextBtn" onclick="scrollCarousel(1)"
                        class="pointer-events-auto w-14 h-14 flex items-center justify-center rounded-full bg-white/5 dark:bg-black/5 backdrop-blur-xl border border-gray-100/50 dark:border-white/5 text-gray-400 hover:text-gray-900 dark:hover:text-white transition-all opacity-0 group-hover/carousel:opacity-100 translate-x-4 group-hover/carousel:translate-x-0 shadow-sm disabled:hidden">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                </div>

                <!-- Carousel Track -->
                <div id="cashboxCarousel"
                    class="flex gap-12 md:gap-20 overflow-x-auto no-scrollbar snap-x snap-mandatory px-[5%] md:px-[20%] py-4 items-center min-h-[350px]">
                    @forelse($cashboxes as $cb)
                        <div class="cashbox-card flex-shrink-0 w-full max-w-3xl snap-center transition-all duration-700 scale-90 opacity-20"
                            data-id="{{ $cb->id }}" data-movements='@json($cb->movements)'>

                            <!-- The "Physical Card" Design -->
                            <div
                                class="relative bg-white dark:bg-gray-900 rounded-[2.5rem] border-2 border-gray-100 dark:border-gray-800 p-10 md:p-12 overflow-hidden transition-all duration-500 hover:border-gray-300 dark:hover:border-gray-600">
                                <!-- Prominent Color Accent: Left vertical bar -->
                                <div class="absolute inset-y-0 left-0 w-4 transition-all duration-500"
                                    style="background-color: {{ $cb->color }}"></div>

                                <!-- Subtle Background Glow -->
                                <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full opacity-[0.03] dark:opacity-[0.05] pointer-events-none"
                                    style="background-color: {{ $cb->color }}"></div>

                                <!-- Card Header -->
                                <div class="flex justify-between items-start mb-12 pl-4">
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-3">
                                            <!-- Card Chip Simulation -->
                                            <div
                                                class="w-10 h-7 rounded-md bg-yellow-400 border border-yellow-500 flex flex-col justify-around p-1.5 opacity-100">
                                                <div class="w-full h-px bg-yellow-700"></div>
                                                <div class="w-full h-px bg-yellow-700"></div>
                                                <div class="w-full h-px bg-yellow-700"></div>
                                            </div>
                                            <h3
                                                class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none">
                                                {{ $cb->name }}
                                            </h3>
                                        </div>
                                        <div class="flex items-center gap-3 ml-1">
                                            <span
                                                class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">{{ $cb->bank_name }}</span>
                                            <div class="w-1 h-1 rounded-full bg-gray-200 dark:bg-gray-800"></div>
                                            <span
                                                class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">{{ $cb->currency }}</span>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button onclick='openCashboxModal(@json($cb))'
                                            class="w-10 h-10 flex items-center justify-center rounded-full text-gray-300 hover:text-gray-900 dark:hover:text-white transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('superadmin.cashboxes.destroy', $cb) }}" method="POST"
                                            onsubmit="return confirm('¿Eliminar esta caja?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="w-10 h-10 flex items-center justify-center rounded-full text-gray-300 hover:text-red-500 transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Balance: Hero Typography -->
                                <div class="space-y-10 pl-4">
                                    <div>
                                        <p
                                            class="text-[9px] text-gray-400 font-black uppercase tracking-[0.3em] mb-4 opacity-60">
                                            Balance Total</p>
                                        <div class="flex items-baseline gap-2">
                                            <span
                                                class="text-3xl font-light text-gray-400 dark:text-gray-600 tabular-nums">{{ $cb->currency === 'USD' ? '$' : 'S/' }}</span>
                                            <span
                                                class="text-6xl md:text-7xl font-bold text-gray-900 dark:text-white tracking-tighter tabular-nums leading-none">
                                                {{ number_format($cb->balance, 2) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Footer Data: Monospaced -->
                                    <div class="grid grid-cols-2 gap-12 pt-8 border-t border-gray-50 dark:border-gray-900">
                                        @if($cb->account_number)
                                            <div class="space-y-1">
                                                <p class="text-[8px] text-gray-400 font-black uppercase tracking-[0.2em]">N° Cuenta
                                                </p>
                                                <p
                                                    class="text-xs font-bold text-gray-900 dark:text-gray-300 font-mono tracking-tighter">
                                                    {{ $cb->account_number }}
                                                </p>
                                            </div>
                                        @endif
                                        @if($cb->cci)
                                            <div class="space-y-1">
                                                <p class="text-[8px] text-gray-400 font-black uppercase tracking-[0.2em]">CCI</p>
                                                <p
                                                    class="text-xs font-bold text-gray-900 dark:text-gray-300 font-mono tracking-tighter">
                                                    {{ $cb->cci }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-32 text-center w-full">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">No hay cajas registradas
                            </p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination Dots -->
                <div class="flex justify-center gap-3 mt-10">
                    @foreach($cashboxes as $index => $cb)
                        <button onclick="scrollToIndex({{ $index }})"
                            class="carousel-dot w-2 h-2 rounded-full bg-gray-200 dark:bg-gray-800 transition-all duration-500"
                            data-index="{{ $index }}"></button>
                    @endforeach
                </div>
            </div>

            <!-- Movements Section: Editorial List -->
            <div id="movementsSection" class="max-w-4xl mx-auto opacity-0 translate-y-8 transition-all duration-1000">
                <div class="flex items-center justify-between mb-12 border-b border-gray-100 dark:border-gray-900 pb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white uppercase tracking-tighter">Actividad
                        Reciente</h2>
                    <button
                        class="text-[9px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        Historial Completo
                    </button>
                </div>

                <div id="movementsList" class="space-y-2">
                    <!-- JS populated -->
                </div>

                <div id="noMovements" class="hidden py-32 text-center">
                    <p class="text-[10px] font-black text-gray-300 dark:text-gray-700 uppercase tracking-[0.3em]">Sin
                        movimientos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Minimalist refinement -->
    <div id="cashboxModal"
        class="hidden fixed inset-0 bg-white/80 dark:bg-black/80 backdrop-blur-2xl z-[100] flex items-center justify-center p-4">
        <div class="w-full max-w-lg animate-in fade-in zoom-in duration-500">
            <div class="px-10 py-12">
                <div class="flex items-center justify-between mb-16">
                    <h3 id="modalTitle" class="text-3xl font-bold text-gray-900 dark:text-white tracking-tighter uppercase">
                        Nueva Caja</h3>
                    <button onclick="closeCashboxModal()"
                        class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="cashboxForm" method="POST" class="space-y-12">
                    @csrf
                    <div id="methodField"></div>

                    <div class="space-y-10">
                        <!-- Inputs: Underline only, bold text -->
                        <div class="space-y-8">
                            <div class="group">
                                <label
                                    class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-2">Nombre</label>
                                <input type="text" name="name" id="cb_name" required placeholder="Letras únicamente"
                                    oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')"
                                    class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-4 text-xl font-bold text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all placeholder:text-gray-200 dark:placeholder:text-gray-800">
                            </div>

                            <div class="group">
                                <label
                                    class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-2">Entidad</label>
                                <select id="cb_bank_select" required onchange="toggleCustomBank(this.value)"
                                    class="w-full bg-transparent dark:bg-gray-950 border-0 border-b-2 border-gray-100 dark:border-gray-900 py-4 text-xl font-bold text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all appearance-none cursor-pointer">
                                    <option value="" disabled selected class="dark:bg-gray-950">Elegir banco</option>
                                    <option value="BCP" class="dark:bg-gray-950">BCP</option>
                                    <option value="Interbank" class="dark:bg-gray-950">Interbank</option>
                                    <option value="BBVA" class="dark:bg-gray-950">BBVA</option>
                                    <option value="Scotiabank" class="dark:bg-gray-950">Scotiabank</option>
                                    <option value="BanBif" class="dark:bg-gray-950">BanBif</option>
                                    <option value="Banco de la Nación" class="dark:bg-gray-950">Banco de la Nación</option>
                                    <option value="Mibanco" class="dark:bg-gray-950">Mibanco</option>
                                    <option value="GNB" class="dark:bg-gray-950">GNB</option>
                                    <option value="Falabella" class="dark:bg-gray-950">Falabella</option>
                                    <option value="Ripley" class="dark:bg-gray-950">Ripley</option>
                                    <option value="Pichincha" class="dark:bg-gray-950">Pichincha</option>
                                    <option value="Comercio" class="dark:bg-gray-950">Comercio</option>
                                    <option value="Efectivo" class="dark:bg-gray-950">Efectivo</option>
                                    <option value="Otros" class="dark:bg-gray-950">Otros</option>
                                </select>
                                <input type="hidden" name="bank_name" id="cb_bank">
                            </div>

                            <div id="customBankWrapper" class="hidden animate-in slide-in-from-top-2 duration-300">
                                <label
                                    class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-1">Nombre
                                    del Banco</label>
                                <input type="text" id="cb_bank_custom" placeholder="Solo letras"
                                    oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')"
                                    class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-4 text-xl font-bold text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all placeholder:text-gray-200 dark:placeholder:text-gray-800">
                            </div>

                            <div class="grid grid-cols-2 gap-16">
                                <div>
                                    <label
                                        class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-1">Moneda</label>
                                    <select name="currency" id="cb_currency" required
                                        class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-4 text-xl font-bold text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all appearance-none cursor-pointer">
                                        <option value="PEN">Soles (S/)</option>
                                        <option value="USD">Dólares ($)</option>
                                    </select>
                                </div>
                                <!-- Color -->
                                <div>
                                    <label
                                        class="text-[9px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-[0.3em] block mb-1">Color</label>
                                    <div
                                        class="relative h-[60px] border-b-2 border-gray-100 dark:border-gray-900 group/color">
                                        <input type="color" name="color" id="cb_color" value="#000000"
                                            class="absolute inset-0 w-full h-full bg-transparent border-0 cursor-pointer overflow-hidden p-0 opacity-0 z-10">
                                        <div id="colorPreview"
                                            class="absolute inset-y-3 left-0 w-full rounded-2xl transition-all border border-gray-100 dark:border-white/10 flex items-center justify-center"
                                            style="background-color: #000000">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <label
                                        class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-1">N°
                                        Cuenta</label>
                                    <input type="text" name="account_number" id="cb_account" placeholder="Opcional"
                                        class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-4 text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all">
                                </div>
                                <div>
                                    <label
                                        class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-1">CCI</label>
                                    <input type="text" name="cci" id="cb_cci" placeholder="Opcional"
                                        class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-4 text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-10 flex items-center justify-between">
                        <button type="button" onclick="closeCashboxModal()"
                            class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 hover:text-gray-900 dark:hover:text-white border-b border-transparent hover:border-gray-900 dark:hover:border-white pb-1 transition-all">
                            Cerrar
                        </button>
                        <button type="submit"
                            class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-white pb-1 hover:opacity-60 transition-opacity">
                            Confirmar Caja
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        #cashboxCarousel {
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
        }

        .cashbox-card.active {
            scale: 1;
            opacity: 1;
        }

        .movement-item {
            animation: slideIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script>
        const carousel = document.getElementById('cashboxCarousel');
        const cards = document.querySelectorAll('.cashbox-card');
        const dots = document.querySelectorAll('.carousel-dot');
        const movementsSection = document.getElementById('movementsSection');
        const movementsList = document.getElementById('movementsList');
        const noMovements = document.getElementById('noMovements');

        function updateActiveCard() {
            let activeIndex = 0;
            let minDistance = Infinity;

            cards.forEach((card, index) => {
                const rect = card.getBoundingClientRect();
                const distance = Math.abs(rect.left + rect.width / 2 - window.innerWidth / 2);

                if (distance < minDistance) {
                    minDistance = distance;
                    activeIndex = index;
                }
                card.classList.remove('active');
            });

            if (cards[activeIndex]) {
                cards[activeIndex].classList.add('active');

                // Update dots
                dots.forEach((dot, index) => {
                    dot.classList.toggle('bg-gray-900', index === activeIndex);
                    dot.classList.toggle('dark:bg-white', index === activeIndex);
                    dot.classList.toggle('w-6', index === activeIndex);
                    dot.classList.toggle('opacity-100', index === activeIndex);
                    dot.classList.toggle('opacity-20', index !== activeIndex);
                });

                // Update arrows visibility
                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');
                if (prevBtn) prevBtn.disabled = activeIndex === 0;
                if (nextBtn) nextBtn.disabled = activeIndex === cards.length - 1;

                // Update movements
                const movements = JSON.parse(cards[activeIndex].dataset.movements);
                renderMovements(movements);
            }
        }

        function renderMovements(movements) {
            movementsSection.classList.remove('opacity-0', 'translate-y-8');
            movementsList.innerHTML = '';

            if (movements.length === 0) {
                noMovements.classList.remove('hidden');
                return;
            }

            noMovements.classList.add('hidden');
            movements.forEach((m, i) => {
                const isIncome = m.type === 'income';
                const date = new Date(m.date).toLocaleDateString('es-PE', { day: '2-digit', month: 'short' });

                const html = `
                                                                            <div class="movement-item px-6 md:px-12 py-8 flex items-center justify-between group hover:bg-gray-50 dark:hover:bg-white/5 transition-all duration-500 rounded-3xl" style="animation-delay: ${i * 0.1}s">
                                                                                <div class="flex items-center gap-8">
                                                                                    <div class="text-[10px] font-black text-gray-300 dark:text-gray-700 uppercase tracking-widest tabular-nums w-12">${date}</div>
                                                                                    <div>
                                                                                        <p class="text-base font-bold text-gray-900 dark:text-white tracking-tight">${m.description}</p>
                                                                                        <p class="text-[9px] text-gray-400 font-black uppercase tracking-[0.2em] mt-1">${m.reference_type || 'Operación'} • ID: ${m.reference_id || '---'}</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="text-right">
                                                                                    <p class="text-xl font-black tabular-nums ${isIncome ? 'text-green-500' : 'text-gray-900 dark:text-white'}">
                                                                                        ${isIncome ? '+' : '-'} ${parseFloat(m.amount).toFixed(2)}
                                                                                    </p>
                                                                                </div>
                                                                            </div>
                                                                        `;
                movementsList.insertAdjacentHTML('beforeend', html);
            });
        }

        function scrollCarousel(direction) {
            const firstCard = document.querySelector('.cashbox-card');
            const cardWidth = firstCard ? firstCard.offsetWidth + 48 : 0;
            carousel.scrollBy({ left: direction * cardWidth, behavior: 'smooth' });
        }

        function scrollToIndex(index) {
            const firstCard = document.querySelector('.cashbox-card');
            const cardWidth = firstCard ? firstCard.offsetWidth + 48 : 0;
            carousel.scrollTo({ left: index * cardWidth, behavior: 'smooth' });
        }

        carousel.addEventListener('scroll', () => {
            clearTimeout(window.scrollTimeout);
            window.scrollTimeout = setTimeout(updateActiveCard, 50);
        });

        // Init
        if (cards.length > 0) {
            setTimeout(updateActiveCard, 100);
        }

        function toggleCustomBank(value) {
            const wrapper = document.getElementById('customBankWrapper');
            const customInput = document.getElementById('cb_bank_custom');
            const hiddenInput = document.getElementById('cb_bank');

            if (value === 'Otros') {
                wrapper.classList.remove('hidden');
                customInput.required = true;
                hiddenInput.value = customInput.value;
            } else {
                wrapper.classList.add('hidden');
                customInput.required = false;
                hiddenInput.value = value;
            }
        }

        document.getElementById('cb_bank_custom').addEventListener('input', function () {
            if (document.getElementById('cb_bank_select').value === 'Otros') {
                document.getElementById('cb_bank').value = this.value;
            }
        });

        document.getElementById('cb_color').addEventListener('input', function () {
            document.getElementById('colorPreview').style.backgroundColor = this.value;
        });

        function openCashboxModal(cb = null) {
            const modal = document.getElementById('cashboxModal');
            const form = document.getElementById('cashboxForm');
            const title = document.getElementById('modalTitle');
            const methodField = document.getElementById('methodField');
            const bankSelect = document.getElementById('cb_bank_select');
            const customInput = document.getElementById('cb_bank_custom');
            const colorInput = document.getElementById('cb_color');
            const colorPreview = document.getElementById('colorPreview');

            if (cb) {
                title.textContent = 'Editar Caja';
                form.action = `/gestion-interna/cajas/${cb.id}`;
                methodField.innerHTML = '@method("PUT")';
                document.getElementById('cb_name').value = cb.name;

                const banks = ['BCP', 'Interbank', 'BBVA', 'Scotiabank', 'BanBif', 'Banco de la Nación', 'Mibanco', 'GNB', 'Falabella', 'Ripley', 'Pichincha', 'Comercio', 'Efectivo'];
                if (banks.includes(cb.bank_name)) {
                    bankSelect.value = cb.bank_name;
                    toggleCustomBank(cb.bank_name);
                } else {
                    bankSelect.value = 'Otros';
                    toggleCustomBank('Otros');
                    customInput.value = cb.bank_name;
                    document.getElementById('cb_bank').value = cb.bank_name;
                }

                document.getElementById('cb_currency').value = cb.currency;
                colorInput.value = cb.color;
                colorPreview.style.backgroundColor = cb.color;
                document.getElementById('cb_account').value = cb.account_number || '';
                document.getElementById('cb_cci').value = cb.cci || '';
            } else {
                title.textContent = 'Nueva Caja';
                form.action = "{{ route('superadmin.cashboxes.store') }}";
                methodField.innerHTML = '';
                form.reset();
                bankSelect.value = "";
                toggleCustomBank("");
                colorInput.value = '#000000';
                colorPreview.style.backgroundColor = '#000000';
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCashboxModal() {
            document.getElementById('cashboxModal').classList.add('hidden');
            document.body.style.overflow = '';
        }
    </script>
@endsection