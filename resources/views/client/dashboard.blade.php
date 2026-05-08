@extends('client.layout')

@section('title', 'Inicio')

@section('content')
<div class="min-h-screen px-5 md:px-10 py-10">

    <!-- Saludo y Perfil Minimalista -->
    <div class="flex flex-col md:flex-row md:items-end gap-6 md:gap-10 mb-16">
        @php
            $selfie = $user->documents?->selfie;
            $profilePhoto = $selfie ? Storage::url($selfie) : ($user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=e5e7eb&color=6b7280');
        @endphp
        
        <div class="relative inline-block">
            <div class="w-24 h-24 md:w-32 md:h-32 rounded-2xl overflow-hidden grayscale hover:grayscale-0 transition-all duration-700">
                <img src="{{ $profilePhoto }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
            </div>
            <div class="absolute -bottom-1 -right-1 bg-green-500 w-3 h-3 rounded-full border-2 border-white dark:border-gray-950"></div>
        </div>

        <div class="flex-1">
            <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em] mb-2">Identidad Verificada</p>
            <h1 class="text-4xl md:text-5xl font-light text-gray-900 dark:text-white tracking-tighter mb-6">
                {{ $user->first_name ?? $user->name }}
            </h1>
            
            <div class="flex flex-wrap gap-x-8 gap-y-4">
                <button type="button" onclick="openBankModal()" class="group inline-flex items-center gap-2 text-[10px] font-bold text-gray-900 dark:text-white uppercase tracking-widest transition-opacity hover:opacity-70">
                    <span class="border-b-2 border-gray-900 dark:border-white pb-1">Gestionar cuentas de banco</span>
                </button>
                <a href="{{ route('client.loans.profile.edit') }}" class="group inline-flex items-center gap-2 text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest transition-opacity hover:opacity-70">
                    <span class="border-b-2 border-transparent group-hover:border-gray-400 dark:group-hover:border-gray-500 pb-1 transition-colors">Editar Perfil</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Estado rápido -->
    <div class="flex items-center gap-6 mb-12 pb-8 border-b border-gray-100 dark:border-gray-900">
        <div>
            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Estado</p>
            <div class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                <span class="text-sm font-medium text-green-600 dark:text-green-400">Verificado</span>
            </div>
        </div>
        <div class="w-px h-8 bg-gray-100 dark:bg-gray-900"></div>
        <div>
            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Préstamos activos</p>
            <p class="text-sm font-medium text-gray-900 dark:text-white">0</p>
        </div>
        <div class="w-px h-8 bg-gray-100 dark:bg-gray-900"></div>
        <div>
            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Miembro desde</p>
            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->created_at->format('d M Y') }}</p>
        </div>
    </div>

    <!-- Datos personales -->
    <div class="mb-10">
        <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-5">Datos personales</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-0">
            @foreach([
                'Nombres'   => $user->first_name,
                'Apellidos' => $user->last_name,
                'DNI'       => $user->dni,
                'Teléfono'  => $user->phone,
                'Correo'    => $user->email,
            ] as $label => $value)
            <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-900">
                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $label }}</span>
                <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $value ?? '—' }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Dirección -->
    <div class="mb-10">
        <p class="text-xs text-gray-600 dark:text-gray-600 uppercase tracking-widest mb-5">Dirección</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-0">
            @foreach([
                'Departamento' => $department,
                'Provincia'    => $province,
                'Distrito'     => $district,
                'Dirección'    => $user->address,
            ] as $label => $value)
            <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-900">
                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $label }}</span>
                <span class="text-xs font-medium text-gray-900 dark:text-white text-right max-w-[220px]">{{ $value ?? '—' }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>

<!-- Modal Gestionar Cuentas de Banco -->
<div id="bankModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <!-- Overlay -->
        <div onclick="closeBankModal()" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

        <!-- Modal content -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-950 rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full relative z-10">
            <div class="px-6 py-8 md:px-10">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">Cuentas Bancarias</h3>
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest mt-1">Donde recibirás tus préstamos</p>
                    </div>
                    <button type="button" onclick="closeBankModal()" class="text-gray-400 hover:text-gray-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Lista de cuentas -->
                <div id="bankAccountsList" class="space-y-4 mb-8">
                    <!-- Dinámico -->
                    <div class="animate-pulse flex space-x-4">
                        <div class="flex-1 space-y-4 py-1">
                            <div class="h-4 bg-gray-100 dark:bg-gray-800 rounded w-3/4"></div>
                            <div class="h-4 bg-gray-100 dark:bg-gray-800 rounded"></div>
                        </div>
                    </div>
                </div>

                <!-- Formulario Agregar/Editar (Oculto por defecto) -->
                <div id="bankAccountForm" class="hidden border-t border-gray-100 dark:border-gray-900 pt-8 mt-8">
                    <h4 id="formTitle" class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-widest mb-6">Agregar Nueva Cuenta</h4>
                    <form id="actualForm" onsubmit="saveBankAccount(event)" class="space-y-6">
                        <input type="hidden" id="editingId" value="">
                        <div class="group">
                            <label class="block text-[10px] font-medium text-gray-400 dark:text-gray-500 mb-1 uppercase">Nombre Personalizado (Alias)</label>
                            <input type="text" id="f_alias" placeholder="Ej: Mi cuenta BCP" class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="group">
                                <label class="block text-[10px] font-medium text-gray-400 dark:text-gray-500 mb-1 uppercase">Banco</label>
                                <select id="f_bank_name" required class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all appearance-none cursor-pointer">
                                    <option value="" class="dark:bg-gray-950">Seleccionar</option>
                                    @foreach(['BCP', 'Interbank', 'BBVA', 'Scotiabank', 'BanBif', 'Banco de la Nación', 'Caja Piura', 'Caja Huancayo', 'Caja Arequipa'] as $bank)
                                        <option value="{{ $bank }}" class="dark:bg-gray-950">{{ $bank }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-medium text-gray-400 dark:text-gray-500 mb-1 uppercase">Tipo de Cuenta</label>
                                <select id="f_account_type" required class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all appearance-none cursor-pointer">
                                    <option value="ahorros" class="dark:bg-gray-950">Ahorros</option>
                                    <option value="corriente" class="dark:bg-gray-950">Corriente</option>
                                </select>
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-medium text-gray-400 dark:text-gray-500 mb-1 uppercase">Número de Cuenta</label>
                                <input type="text" id="f_account_number" required placeholder="Ej: 19134567890123" 
                                       maxlength="14"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                       class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all tabular-nums">
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-medium text-gray-400 dark:text-gray-500 mb-1 uppercase">CCI (Opcional)</label>
                                <input type="text" id="f_cci" placeholder="Ej: 00219100123456789012" 
                                       maxlength="20"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                       class="w-full bg-transparent border-b border-gray-200 dark:border-gray-800 text-sm py-2 focus:outline-none focus:border-gray-900 dark:focus:border-white transition-all tabular-nums">
                            </div>
                        </div>
                        <div class="flex gap-4 pt-4">
                            <button type="submit" class="flex-1 py-3 rounded-xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[10px] font-bold uppercase tracking-widest hover:opacity-90 transition-opacity">Guardar Cuenta</button>
                            <button type="button" onclick="hideForm()" class="px-6 py-3 rounded-xl bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 text-[10px] font-bold uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors">Cancelar</button>
                        </div>
                    </form>
                </div>

                <div id="addBtnContainer" class="pt-4">
                    <button type="button" onclick="showForm()" class="w-full py-4 rounded-2xl border-2 border-dashed border-gray-100 dark:border-gray-800 text-gray-400 dark:text-gray-500 text-xs font-medium hover:border-gray-300 dark:hover:border-gray-700 hover:text-gray-600 dark:hover:text-gray-300 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Agregar otra cuenta
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let accounts = [];

    async function openBankModal() {
        document.getElementById('bankModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        await loadAccounts();
    }

    function closeBankModal() {
        document.getElementById('bankModal').classList.add('hidden');
        document.body.style.overflow = '';
        hideForm();
    }

    async function loadAccounts() {
        const list = document.getElementById('bankAccountsList');
        try {
            const response = await fetch('{{ route("client.bank-accounts.index") }}');
            accounts = await response.json();
            
            if (accounts.length === 0) {
                list.innerHTML = `<div class="py-10 text-center"><p class="text-xs text-gray-400">No tienes cuentas registradas.</p></div>`;
                return;
            }

            list.innerHTML = accounts.map(acc => `
                <div class="group relative bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-5 border border-transparent hover:border-gray-100 dark:hover:border-gray-800 transition-all">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] font-bold text-gray-900 dark:text-white uppercase tracking-widest">${acc.alias || acc.bank_name}</span>
                                <span class="px-1.5 py-0.5 bg-gray-200 dark:bg-gray-800 text-[8px] font-bold rounded uppercase tracking-tighter text-gray-600 dark:text-gray-400">${acc.bank_name} - ${acc.account_type}</span>
                            </div>
                            <p class="text-xs font-medium text-gray-900 dark:text-white tabular-nums">${acc.account_number}</p>
                            ${acc.cci ? `<p class="text-[10px] text-gray-400 mt-1 tabular-nums">CCI: ${acc.cci}</p>` : ''}
                        </div>
                        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="editAccount(${acc.id})" class="p-1.5 text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            <button onclick="deleteAccount(${acc.id})" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        } catch (e) {
            list.innerHTML = `<p class="text-red-500 text-xs">Error al cargar cuentas.</p>`;
        }
    }

    function showForm() {
        document.getElementById('bankAccountForm').classList.remove('hidden');
        document.getElementById('addBtnContainer').classList.add('hidden');
        document.getElementById('bankAccountsList').classList.add('opacity-50', 'pointer-events-none');
    }

    function hideForm() {
        document.getElementById('bankAccountForm').classList.add('hidden');
        document.getElementById('addBtnContainer').classList.remove('hidden');
        document.getElementById('bankAccountsList').classList.remove('opacity-50', 'pointer-events-none');
        document.getElementById('actualForm').reset();
        document.getElementById('editingId').value = '';
        document.getElementById('formTitle').textContent = 'Agregar Nueva Cuenta';
    }

    function editAccount(id) {
        const acc = accounts.find(a => a.id === id);
        if (!acc) return;
        
        document.getElementById('editingId').value = acc.id;
        document.getElementById('f_alias').value = acc.alias || '';
        document.getElementById('f_bank_name').value = acc.bank_name;
        document.getElementById('f_account_type').value = acc.account_type;
        document.getElementById('f_account_number').value = acc.account_number;
        document.getElementById('f_cci').value = acc.cci || '';
        document.getElementById('formTitle').textContent = 'Editar Cuenta';
        
        showForm();
    }

    async function saveBankAccount(e) {
        e.preventDefault();
        const id = document.getElementById('editingId').value;
        const data = {
            alias: document.getElementById('f_alias').value,
            bank_name: document.getElementById('f_bank_name').value,
            account_type: document.getElementById('f_account_type').value,
            account_number: document.getElementById('f_account_number').value,
            cci: document.getElementById('f_cci').value,
            _token: '{{ csrf_token() }}'
        };

        const url = id ? `/panel/cuentas-bancarias/${id}` : '{{ route("client.bank-accounts.store") }}';
        const method = id ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                hideForm();
                await loadAccounts();
            } else {
                alert(result.message || 'Error al guardar');
            }
        } catch (e) {
            alert('Error de conexión');
        }
    }

    async function deleteAccount(id) {
        if (!confirm('¿Estás seguro de eliminar esta cuenta?')) return;
        try {
            const response = await fetch(`/panel/cuentas-bancarias/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (result.success) await loadAccounts();
        } catch (e) {
            alert('Error al eliminar');
        }
    }
</script>
@endsection
