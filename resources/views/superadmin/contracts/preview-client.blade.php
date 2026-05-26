<div class="doc-page {{ \App\Models\CompanySetting::get('company_logo') ? 'has-logo' : '' }}">
    @if($companyLogo = \App\Models\CompanySetting::get('company_logo'))
        <img src="{{ Storage::url($companyLogo) }}" alt="Logo" class="doc-logo">
    @endif
    
    <h1 class="doc-title">{{ $contract->name }}</h1>
    <hr class="doc-divider">
    
    @foreach($renderedClauses as $clause)
        @if(!empty($clause['is_box']))
            {!! $clause['content'] !!}
        @else
            <div class="doc-clause">
                @if($clause['title'])
                    <h4 class="doc-clause-title">{{ $clause['title'] }}</h4>
                @endif
                <div class="doc-clause-content">
                    {!! nl2br($clause['content']) !!}
                </div>
            </div>
        @endif
    @endforeach
    
    <hr class="doc-divider">
    
    <div class="doc-footer">
        <table style="width:100%;border-collapse:collapse">
            <tr>
                <td style="width:50%;vertical-align:bottom;text-align:center;padding:0 10px">
                    <div class="doc-signature-line"></div>
                    <p class="sig-name">{{ $user->name }}</p>
                    <p class="sig-label">DNI: {{ $user->dni }}</p>
                </td>
                <td style="width:50%;vertical-align:bottom;text-align:center;padding:0 10px">
                    @if($companySignature = \App\Models\CompanySetting::get('company_signature'))
                        <img src="{{ Storage::url($companySignature) }}" alt="Firma" class="doc-signature-img">
                    @endif
                    <div class="doc-signature-line"></div>
                    <p class="sig-name">Juan Carlos Pérez García</p>
                    <p class="sig-label">Representante Legal</p>
                </td>
            </tr>
        </table>
    </div>
</div>