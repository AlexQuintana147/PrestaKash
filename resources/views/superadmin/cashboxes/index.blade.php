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
                    class="absolute inset-y-0 left-0 right-0 pointer-events-none z-20 hidden md:flex justify-between items-center px-4 md:px-10">
                    <button id="prevBtn" onclick="scrollCarousel(-1)"
                        class="pointer-events-auto w-12 h-12 flex items-center justify-center rounded-full bg-white/10 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 text-gray-400 hover:text-gray-900 dark:hover:text-white transition-all opacity-0 group-hover/carousel:opacity-100 -translate-x-4 group-hover/carousel:translate-x-0 disabled:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button id="nextBtn" onclick="scrollCarousel(1)"
                        class="pointer-events-auto w-12 h-12 flex items-center justify-center rounded-full bg-white/10 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 text-gray-400 hover:text-gray-900 dark:hover:text-white transition-all opacity-0 group-hover/carousel:opacity-100 translate-x-4 group-hover/carousel:translate-x-0 disabled:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- Edge Blurs -->
                <div
                    class="absolute inset-y-0 left-0 w-32 bg-gradient-to-r from-white dark:from-gray-950 via-white/80 dark:via-gray-950/80 to-transparent z-10 pointer-events-none backdrop-blur-[2px] hidden md:block">
                </div>
                <div
                    class="absolute inset-y-0 right-0 w-32 bg-gradient-to-l from-white dark:from-gray-950 via-white/80 dark:via-gray-950/80 to-transparent z-10 pointer-events-none backdrop-blur-[2px] hidden md:block">
                </div>

                <!-- Carousel Track -->
                <div id="cashboxCarousel"
                    class="flex gap-4 md:gap-12 overflow-x-auto no-scrollbar snap-x snap-mandatory px-4 md:px-[20%] py-2 md:py-4 items-center min-h-[220px] md:min-h-[280px]">
                    @forelse($cashboxes as $cb)
                        <div class="cashbox-card flex-shrink-0 w-[85%] md:w-full max-w-2xl snap-center transition-all duration-700 scale-95 md:scale-90 opacity-40 md:opacity-20"
                            data-id="{{ $cb->id }}" data-movements='@json($cb->movements)'>

                            <!-- The "Physical Card" Design -->
                            <div
                                class="relative bg-white dark:bg-gray-900 rounded-[1.25rem] md:rounded-[2rem] border-2 border-gray-100 dark:border-gray-800 p-4 md:p-8 overflow-hidden transition-all duration-500 hover:border-gray-300 dark:hover:border-gray-600">
                                <!-- Prominent Color Accent: Left vertical bar -->
                                <div class="absolute inset-y-0 left-0 w-1.5 md:w-2.5 transition-all duration-500"
                                    style="background-color: {{ $cb->color }}"></div>

                                <!-- Subtle Background Glow -->
                                <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full opacity-[0.03] dark:opacity-[0.05] pointer-events-none"
                                    style="background-color: {{ $cb->color }}"></div>

                                <!-- Card Header -->
                                <div class="flex justify-between items-start mb-3 md:mb-6 pl-2 md:pl-4">
                                    <div class="space-y-0.5 md:space-y-1">
                                        <div class="flex items-center gap-2 md:gap-3">
                                            <!-- Card Chip Simulation -->
                                            <div
                                                class="w-7 h-4.5 md:w-10 md:h-7 rounded-sm md:rounded-md bg-yellow-400 border border-yellow-500 flex flex-col justify-around p-1 md:p-1.5 opacity-100">
                                                <div class="w-full h-px bg-yellow-700"></div>
                                                <div class="w-full h-px bg-yellow-700"></div>
                                                <div class="w-full h-px bg-yellow-700"></div>
                                            </div>
                                            <h3
                                                class="text-base md:text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none">
                                                {{ $cb->name }}
                                            </h3>
                                        </div>
                                        <div class="flex items-center gap-2 md:gap-3 ml-0.5 md:ml-1">
                                            <span
                                                class="text-[7px] md:text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">{{ $cb->bank_name }}</span>
                                            <div class="w-0.5 h-0.5 rounded-full bg-gray-200 dark:bg-gray-800"></div>
                                            <span
                                                class="cb-currency-label text-[7px] md:text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">{{ $cb->currency }}</span>
                                        </div>
                                    </div>
                                    <div class="flex gap-0 md:gap-1">
                                        <!-- Add Movement Button -->
                                        <button onclick="openMovementModal({{ $cb->id }}, 'income', '{{ $cb->currency }}')"
                                            class="w-6 h-6 md:w-8 md:h-8 flex items-center justify-center rounded-full text-green-500 hover:bg-green-500/10 transition-all"
                                            title="Registrar Ingreso">
                                            <svg class="w-3.5 h-3.5 md:w-5 md:h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                        <button onclick="openMovementModal({{ $cb->id }}, 'expense', '{{ $cb->currency }}')"
                                            class="w-6 h-6 md:w-8 md:h-8 flex items-center justify-center rounded-full text-red-500 hover:bg-red-500/10 transition-all"
                                            title="Registrar Egreso">
                                            <svg class="w-3.5 h-3.5 md:w-5 md:h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M20 12H4" />
                                            </svg>
                                        </button>

                                        <div class="w-px h-3.5 md:h-5 bg-gray-100 dark:bg-gray-800 mx-0.5 md:mx-1 self-center">
                                        </div>

                                        <button onclick='openCashboxModal(@json($cb))'
                                            class="w-6 h-6 md:w-8 md:h-8 flex items-center justify-center rounded-full text-gray-300 hover:text-gray-900 dark:hover:text-white transition-all">
                                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('superadmin.cashboxes.destroy', $cb) }}" method="POST"
                                            onsubmit="return confirm('¿Eliminar esta caja?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="w-6 h-6 md:w-8 md:h-8 flex items-center justify-center rounded-full text-gray-300 hover:text-red-500 transition-all">
                                                <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Balance: Hero Typography -->
                                <div class="space-y-2 md:space-y-4 pl-2 md:pl-4">
                                    <div>
                                        <p
                                            class="text-[7px] md:text-[9px] text-gray-400 font-black uppercase tracking-[0.3em] mb-0.5 md:mb-2 opacity-60">
                                            Balance Total</p>
                                        <div class="flex items-baseline gap-1 md:gap-2">
                                            <span
                                                class="text-lg md:text-2xl font-light text-gray-400 dark:text-gray-600 tabular-nums">{{ $cb->currency === 'USD' ? '$' : 'S/' }}</span>
                                            <span
                                                class="text-3xl md:text-6xl font-bold text-gray-900 dark:text-white tracking-tighter tabular-nums leading-none">
                                                {{ number_format($cb->balance, 2) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Footer Data: Monospaced -->
                                    <div
                                        class="grid grid-cols-2 gap-2 md:gap-8 pt-3 md:pt-6 border-t border-gray-50 dark:border-gray-900">
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

                                    <!-- Manual Movements Buttons -->
                                    <div
                                        class="mt-6 flex gap-4 pt-4 border-t border-gray-50 dark:border-gray-900 opacity-0 translate-y-4 transition-all duration-500 group-hover:opacity-100 group-hover:translate-y-0">
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

                <!-- Pagination Container -->
                <div id="movementsPagination" class="mt-12 flex items-center justify-center gap-4">
                    <!-- JS populated -->
                </div>

                <div id="noMovements" class="hidden py-32 text-center">
                    <p class="text-[10px] font-black text-gray-300 dark:text-gray-700 uppercase tracking-[0.3em]">Sin
                        movimientos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Caja Minimalista -->
    <div id="cashboxModal"
        class="hidden fixed inset-0 bg-white/80 dark:bg-black/80 backdrop-blur-2xl z-[100] flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-950 w-full max-w-lg animate-in fade-in zoom-in duration-500">
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

    <!-- Modal Movimiento Minimalista -->
    <div id="movementModal"
        class="hidden fixed inset-0 bg-white/80 dark:bg-black/80 backdrop-blur-2xl z-[100] flex items-center justify-center p-4">
        <div class="w-full max-w-md animate-in fade-in zoom-in duration-500">
            <div class="px-10 py-12">
                <div class="flex items-center justify-between mb-16">
                    <h3 id="movModalTitle"
                        class="text-3xl font-bold text-gray-900 dark:text-white tracking-tighter uppercase">Movimiento</h3>
                    <button onclick="closeMovementModal()"
                        class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="movementForm" method="POST" class="space-y-12">
                    @csrf
                    <input type="hidden" name="type" id="mov_type">

                    <div class="space-y-10">
                        <!-- Monto con Máscara -->
                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-1">Monto a
                                Registrar</label>
                            <div class="relative">
                                <input type="text" name="amount" id="mov_amount" required placeholder="0.00"
                                    class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-4 text-4xl font-black text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all tabular-nums">
                                <span id="mov_currency_label"
                                    class="absolute right-0 bottom-4 text-xl font-bold text-gray-300 dark:text-gray-700">PEN</span>
                            </div>
                        </div>

                        <!-- Motivo / Descripción -->
                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-1">Motivo
                                / Origen</label>
                            <input type="text" name="description" id="mov_description" required
                                placeholder="Ej: Pago de servicios, Inyección de capital..."
                                oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')"
                                class="w-full bg-transparent border-0 border-b-2 border-gray-100 dark:border-gray-900 py-4 text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all">
                        </div>
                    </div>

                    <div class="pt-10 flex items-center justify-between">
                        <button type="button" onclick="closeMovementModal()"
                            class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 hover:text-gray-900 dark:hover:text-white border-b border-transparent hover:border-gray-900 dark:hover:border-white pb-1 transition-all">
                            Cancelar
                        </button>
                        <button type="submit" id="movSubmitBtn"
                            class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-white pb-1 hover:opacity-60 transition-opacity">
                            Confirmar
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

        .cashbox-card {
            filter: blur(8px);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .cashbox-card.active {
            scale: 1;
            opacity: 1;
            filter: blur(0);
        }

        .cashbox-card.active .mt-12 {
            opacity: 1;
            transform: translateY(0);
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
        const movementsPagination = document.getElementById('movementsPagination');

        let currentMovements = [];
        let currentSymbol = 'S/';
        let currentPage = 1;
        const itemsPerPage = 10;

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
                const activeCard = cards[activeIndex];
                activeCard.classList.add('active');

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
                currentMovements = JSON.parse(activeCard.dataset.movements);
                const currency = activeCard.querySelector('.cb-currency-label').textContent;
                currentSymbol = currency === 'USD' ? '$' : 'S/';
                currentPage = 1;
                renderMovements();
            }
        }

        function renderMovements() {
            movementsSection.classList.remove('opacity-0', 'translate-y-8');
            movementsList.innerHTML = '';
            movementsPagination.innerHTML = '';

            if (currentMovements.length === 0) {
                noMovements.classList.remove('hidden');
                return;
            }

            noMovements.classList.add('hidden');

            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedItems = currentMovements.slice(start, end);

            paginatedItems.forEach((m, i) => {
                const isIncome = m.type === 'income';
                const dateObj = new Date(m.date);
                const date = dateObj.toLocaleDateString('es-PE', { day: '2-digit', month: 'short' });
                const time = dateObj.toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit' });
                const userName = m.user ? m.user.name : 'Sistema';

                const html = `
                    <div class="movement-item px-6 md:px-12 py-8 flex items-center justify-between group hover:bg-gray-50 dark:hover:bg-white/5 transition-all duration-500 rounded-3xl" style="animation-delay: ${i * 0.05}s">
                        <div class="flex items-center gap-8">
                            <div class="flex flex-col items-center w-12 flex-shrink-0">
                                <span class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest tabular-nums">${date}</span>
                                <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest tabular-nums">${time}</span>
                            </div>
                            <div>
                                <p class="text-base font-bold text-gray-900 dark:text-white tracking-tight">${m.description}</p>
                                <p class="text-[9px] text-gray-400 font-black uppercase tracking-[0.2em] mt-1">
                                    ${m.reference_type || 'Operación'} • Registrado por: <span class="text-gray-600 dark:text-gray-300 font-bold">${userName}</span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-black tabular-nums ${isIncome ? 'text-green-500' : 'text-red-500'}">
                                ${isIncome ? '+' : '-'} ${currentSymbol} ${parseFloat(m.amount).toFixed(2)}
                            </p>
                        </div>
                    </div>
                `;
                movementsList.insertAdjacentHTML('beforeend', html);
            });

            renderPagination();
        }

        function renderPagination() {
            const totalPages = Math.ceil(currentMovements.length / itemsPerPage);
            if (totalPages <= 1) return;

            let html = '';

            // Previous button
            html += `
                <button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} 
                    class="p-2 text-gray-400 hover:text-gray-900 dark:hover:text-white disabled:opacity-0 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            `;

            // Page numbers (simplified for now, showing all if few, or range)
            for (let i = 1; i <= totalPages; i++) {
                const isActive = i === currentPage;
                html += `
                    <button onclick="changePage(${i})" 
                        class="w-8 h-8 rounded-full text-[10px] font-black uppercase tracking-widest transition-all ${isActive ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : 'text-gray-400 hover:text-gray-900 dark:hover:text-white'}">
                        ${i}
                    </button>
                `;
            }

            // Next button
            html += `
                <button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} 
                    class="p-2 text-gray-400 hover:text-gray-900 dark:hover:text-white disabled:opacity-0 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            `;

            movementsPagination.innerHTML = html;
        }

        function changePage(page) {
            const totalPages = Math.ceil(currentMovements.length / itemsPerPage);
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderMovements();
            
            // Scroll to movements section top
            movementsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
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

        function openMovementModal(cashboxId, type, currency) {
            const modal = document.getElementById('movementModal');
            const form = document.getElementById('movementForm');
            const title = document.getElementById('movModalTitle');
            const typeInput = document.getElementById('mov_type');
            const currencyLabel = document.getElementById('mov_currency_label');
            const amountInput = document.getElementById('mov_amount');

            typeInput.value = type;
            currencyLabel.textContent = currency;
            form.action = `/gestion-interna/cajas/${cashboxId}/movimiento`;
            title.textContent = type === 'income' ? 'Registrar Ingreso' : 'Registrar Egreso';

            amountInput.value = '';
            document.getElementById('mov_description').value = '';

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeMovementModal() {
            document.getElementById('movementModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.getElementById('mov_amount').addEventListener('input', function (e) {
            let value = this.value.replace(/\D/g, '');
            if (value === '') {
                this.value = '';
                return;
            }

            let cents = parseInt(value);
            let formatted = (cents / 100).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            this.value = formatted;
        });

        document.getElementById('movementForm').addEventListener('submit', function (e) {
            const amountInput = document.getElementById('mov_amount');
            // Remove commas before submit so Laravel validates numeric
            amountInput.value = amountInput.value.replace(/,/g, '');

            const btn = document.getElementById('movSubmitBtn');
            btn.disabled = true;
            btn.opacity = '0.5';
        });
    </script>
@endsection