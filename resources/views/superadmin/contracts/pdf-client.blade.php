<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; }
        body {
            font-family: Georgia, 'Times New Roman', 'DejaVu Serif', serif;
            font-size: 13px;
            line-height: 1.85;
            color: #333;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .doc-page {
            position: relative;
            max-width: 650px;
            margin: 0 auto;
            background: white;
            padding: 48px 44px 40px;
            border: 1px solid #eee;
        }
        .doc-page::before {
            content: '';
            position: absolute;
            top: 16px;
            right: 20px;
            width: 36px;
            height: 48px;
            border: 1.5px solid #ddd;
            border-radius: 2px;
            opacity: 0.5;
        }
        .doc-page.has-logo::before {
            display: none;
        }
        .doc-logo {
            position: absolute;
            top: 12px;
            right: 20px;
            max-width: 90px;
            max-height: 56px;
            object-fit: contain;
        }
        .doc-title {
            text-align: center;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 22px;
            font-weight: 700;
            color: #1a1a1a;
            letter-spacing: 0.02em;
            margin-bottom: 6px;
            line-height: 1.35;
        }
        .doc-subtitle {
            text-align: center;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            color: #888;
            margin-bottom: 32px;
            font-weight: 500;
        }
        .doc-divider {
            height: 1px;
            background: #ccc;
            margin: 28px 0;
            border: none;
        }
        .doc-clause {
            margin-bottom: 22px;
        }
        .doc-clause-title {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 14px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 10px;
            letter-spacing: 0.01em;
        }
        .doc-clause-content {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 13px;
            line-height: 1.85;
            color: #333;
            text-align: justify;
            word-wrap: break-word;
        }
        .doc-clause-content p {
            margin: 0 0 10px 0;
        }
        .doc-footer {
            margin-top: 36px;
            padding-top: 20px;
            border-top: 1px solid #e8e8e8;
            width: 100%;
        }
        .doc-footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .doc-footer-table td {
            width: 50%;
            vertical-align: bottom;
            text-align: center;
            padding: 0 10px;
        }
        .doc-signature-line {
            width: 70%;
            height: 1px;
            background: #aaa;
            margin: 0 auto;
        }
        .doc-signature-img {
            max-height: 50px;
            margin-bottom: 4px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .sig-name {
            font-size: 9px;
            color: #666;
            margin-top: 5px;
            text-align: center;
        }
        .sig-label {
            font-size: 8px;
            color: #888;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="doc-page {{ \App\Models\CompanySetting::get('company_logo') ? 'has-logo' : '' }}">
        @if($companyLogo = \App\Models\CompanySetting::get('company_logo'))
            <img src="{{ storage_path('app/public/' . ltrim($companyLogo, '/')) }}" alt="Logo" class="doc-logo">
        @endif

        <h1 class="doc-title">{{ $contract->name }}</h1>
        <p class="doc-subtitle">Contrato entre {{ $user->name }} y PrestaKash Soluciones Financieras S.A.C.</p>

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
            <table class="doc-footer-table">
                <tr>
                    <td>
                        <div class="doc-signature-line"></div>
                        <p class="sig-name">{{ $user->name }}</p>
                        <p class="sig-label">DNI: {{ $user->dni }}</p>
                    </td>
                    <td>
                        @if($companySignature = \App\Models\CompanySetting::get('company_signature'))
                            <img src="{{ storage_path('app/public/' . ltrim($companySignature, '/')) }}" alt="Firma" class="doc-signature-img">
                        @endif
                        <div class="doc-signature-line"></div>
                        <p class="sig-name">Juan Carlos Pérez García</p>
                        <p class="sig-label">Representante Legal</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
