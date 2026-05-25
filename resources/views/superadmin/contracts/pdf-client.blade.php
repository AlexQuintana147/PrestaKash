<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; }
        body {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20mm;
        }
        .doc-page {
            width: 100%;
            background: white;
            padding: 0;
            margin: 0;
        }
        .doc-logo {
            max-width: 90px;
            max-height: 56px;
            object-fit: contain;
            display: block;
            margin: 0 auto 10px;
        }
        .doc-title {
            font-size: 22pt;
            text-align: center;
            font-weight: bold;
            margin-bottom: 10pt;
            color: #000;
        }
        .doc-subtitle {
            font-size: 12pt;
            text-align: center;
            color: #666;
            margin-bottom: 20pt;
        }
        .doc-divider {
            border: none;
            border-top: 1pt solid #ccc;
            margin: 20pt 0;
        }
        .doc-clause {
            margin-bottom: 18pt;
        }
        .doc-clause-title {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 15pt;
            margin-bottom: 5pt;
            color: #000;
        }
        .doc-clause-content {
            font-size: 11pt;
            line-height: 1.6;
            text-align: justify;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .doc-footer {
            margin-top: 40pt;
            width: 100%;
        }
        .doc-signature-block {
            width: 45%;
            display: inline-block;
            vertical-align: top;
            text-align: center;
        }
        .doc-signature-line {
            border-top: 1pt solid black;
            width: 70%;
            margin: 40pt auto 5pt;
            height: 0;
        }
        .doc-signature-img {
            max-width: 150px;
            display: block;
            margin: 0 auto;
        }
        .sig-name {
            font-size: 9pt;
            color: #666;
            margin-top: 5pt;
            text-align: center;
        }
        .sig-label {
            font-size: 8pt;
            color: #888;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="doc-page">
        @if($companyLogo = \App\Models\CompanySetting::get('company_logo'))
            <img src="{{ storage_path('app/public/' . ltrim($companyLogo, '/')) }}" alt="Logo" class="doc-logo">
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
                <p class="sig-name">{{ $user->name }}</p>
                <p class="sig-label">DNI: {{ $user->dni }}</p>
            </div>
            <div class="doc-signature-block">
                @if($companySignature = \App\Models\CompanySetting::get('company_signature'))
                    <img src="{{ storage_path('app/public/' . ltrim($companySignature, '/')) }}" alt="Firma" class="doc-signature-img">
                @endif
                <div class="doc-signature-line"></div>
                <p class="sig-name">Juan Carlos Pérez García</p>
                <p class="sig-label">Representante Legal</p>
            </div>
        </div>
    </div>
</body>
</html>
