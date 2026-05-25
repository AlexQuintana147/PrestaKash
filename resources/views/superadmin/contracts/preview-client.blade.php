<div class="doc-page {{ \App\Models\CompanySetting::get('company_logo') ? 'has-logo' : '' }}">
    @if($companyLogo = \App\Models\CompanySetting::get('company_logo'))
        <img src="{{ Storage::url($companyLogo) }}" alt="Logo" class="doc-logo">
    @endif
    
    <h1 class="doc-title">{{ $contract->name }}</h1>
    <p class="doc-subtitle">Contrato entre {{ $user->name }} y PrestaKash Soluciones Financieras S.A.C.</p>
    
    <hr class="doc-divider">
    
    @foreach($renderedClauses as $clause)
        <div class="doc-clause">
            @if($clause['title'])
                <h4 class="doc-clause-title">{{ $clause['title'] }}</h4>
            @endif
            <div class="doc-clause-content">
                {!! $clause['content'] !!}
            </div>
        </div>
    @endforeach
    
    <hr class="doc-divider">
    
    <div class="doc-footer">
        <div class="doc-signature-block">
            <div class="doc-signature-line"></div>
            <p class="mt-2 text-[9px] text-gray-400 text-center">{{ $user->name }}</p>
            <p class="text-[8px] text-gray-400">DNI: {{ $user->dni }}</p>
        </div>
        <div class="doc-signature-block">
            @if($companySignature = \App\Models\CompanySetting::get('company_signature'))
                <img src="{{ Storage::url($companySignature) }}" alt="Firma" class="doc-signature-img">
            @endif
            <div class="doc-signature-line"></div>
            <p class="mt-2 text-[9px] text-gray-400 text-center">Juan Carlos Pérez García</p>
            <p class="text-[8px] text-gray-400">Representante Legal</p>
        </div>
    </div>
</div>