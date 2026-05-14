@extends('superadmin.layout')

@section('title', 'Gestión de Cajas')

@section('content')
    <div class="px-5 md:px-10 py-8">

        <!-- Header -->
        <div class="flex items-end justify-between mb-8">
            <div>
                <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-1">Panel Administrativo</p>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Gestión de Cajas
                </h1>
            </div>
            <button onclick="openCashboxModal()"
                class="text-[10px] font-black uppercase tracking-widest text-gray-900 dark:text-white border-b border-gray-900 dark:border-white pb-1 hover:opacity-60 transition-opacity">
                Nueva Caja
            </button>
        </div>

        @if(session('success'))
            <div class="mb-6 flex items-center gap-3 text-sm text-green-500">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Grid de Cajas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($cashboxes as $cb)
                <div
                    class="group relative bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm hover:shadow-xl transition-all duration-300">
                    <!-- Color Indicator -->
                    <div class="absolute top-0 left-0 w-full h-1.5 rounded-t-3xl" style="background-color: {{ $cb->color }}">
                    </div>

                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">
                                {{ $cb->name }}</h3>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">{{ $cb->bank_name }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="openCashboxModal({{ json_encode($cb) }})"
                                class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <form action="{{ route('superadmin.cashboxes.destroy', $cb) }}" method="POST"
                                onsubmit="return confirm('¿Eliminar esta caja?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-end">
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Saldo Actual</p>
                            <p class="text-xl font-black text-gray-900 dark:text-white tabular-nums">
                                {{ $cb->currency === 'USD' ? '$' : 'S/' }} {{ number_format($cb->balance, 2) }}
                            </p>
                        </div>

                        <div class="pt-3 border-t border-gray-50 dark:border-gray-800 space-y-1">
                            @if($cb->account_number)
                                <div class="flex justify-between text-[10px]">
                                    <span class="text-gray-400 font-bold uppercase">N° Cuenta</span>
                                    <span class="text-gray-900 dark:text-gray-300 font-mono">{{ $cb->account_number }}</span>
                                </div>
                            @endif
                            @if($cb->cci)
                                <div class="flex justify-between text-[10px]">
                                    <span class="text-gray-400 font-bold uppercase">CCI</span>
                                    <span class="text-gray-900 dark:text-gray-300 font-mono">{{ $cb->cci }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center">
                    <div
                        class="w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-700" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest mb-1">No hay cajas
                        registradas</h3>
                    <p class="text-xs text-gray-500">Comienza creando una caja para gestionar la liquidez.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Caja -->
    <div id="cashboxModal"
        class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-gray-950 w-full max-w-md rounded-3xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="px-8 pt-8 pb-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 id="modalTitle" class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">
                        Nueva Caja</h3>
                    <button onclick="closeCashboxModal()"
                        class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="cashboxForm" method="POST" class="space-y-5">
                    @csrf
                    <div id="methodField"></div>

                    <div class="space-y-4">
                        <!-- Nombre -->
                        <div>
                            <label
                                class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1.5 ml-1">Nombre
                                de la Caja</label>
                            <input type="text" name="name" id="cb_name" required placeholder="Ej: Caja Principal"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition-all">
                        </div>

                        <!-- Banco -->
                        <div>
                            <label
                                class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1.5 ml-1">Banco
                                / Entidad</label>
                            <input type="text" name="bank_name" id="cb_bank" required
                                placeholder="Ej: BCP, Interbank, Efectivo"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition-all">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Moneda -->
                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1.5 ml-1">Moneda</label>
                                <select name="currency" id="cb_currency" required
                                    class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition-all appearance-none">
                                    <option value="PEN">Soles (S/)</option>
                                    <option value="USD">Dólares ($)</option>
                                </select>
                            </div>
                            <!-- Color -->
                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1.5 ml-1">Color</label>
                                <div class="flex gap-2 items-center h-[44px]">
                                    <input type="color" name="color" id="cb_color" value="#000000"
                                        class="w-full h-full bg-transparent border-0 cursor-pointer rounded-xl overflow-hidden p-0">
                                </div>
                            </div>
                        </div>

                        <!-- N° Cuenta -->
                        <div>
                            <label
                                class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1.5 ml-1">Número
                                de Cuenta (Opcional)</label>
                            <input type="text" name="account_number" id="cb_account" placeholder="000-0000000-0-00"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition-all">
                        </div>

                        <!-- CCI -->
                        <div>
                            <label
                                class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1.5 ml-1">CCI
                                (Opcional)</label>
                            <input type="text" name="cci" id="cb_cci" placeholder="000-000-000000000000-00"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition-all">
                        </div>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" onclick="closeCashboxModal()"
                            class="flex-1 px-6 py-3 text-xs font-black uppercase tracking-widest text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="flex-1 bg-gray-900 dark:bg-white text-white dark:text-black px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest hover:opacity-80 transition-all shadow-lg shadow-gray-500/20">
                            Guardar Caja
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCashboxModal(cb = null) {
            const modal = document.getElementById('cashboxModal');
            const form = document.getElementById('cashboxForm');
            const title = document.getElementById('modalTitle');
            const methodField = document.getElementById('methodField');

            if (cb) {
                title.textContent = 'Editar Caja';
                form.action = `/gestion-interna/cajas/${cb.id}`;
                methodField.innerHTML = '@method("PUT")';
                document.getElementById('cb_name').value = cb.name;
                document.getElementById('cb_bank').value = cb.bank_name;
                document.getElementById('cb_currency').value = cb.currency;
                document.getElementById('cb_color').value = cb.color;
                document.getElementById('cb_account').value = cb.account_number || '';
                document.getElementById('cb_cci').value = cb.cci || '';
            } else {
                title.textContent = 'Nueva Caja';
                form.action = "{{ route('superadmin.cashboxes.store') }}";
                methodField.innerHTML = '';
                form.reset();
                document.getElementById('cb_color').value = '#000000';
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCashboxModal() {
            document.getElementById('cashboxModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Cerrar modal al hacer click fuera
        document.getElementById('cashboxModal').addEventListener('click', function (e) {
            if (e.target === this) closeCashboxModal();
        });
    </script>
@endsection