<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista Previa - {{ $contract->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Serif', Georgia, 'Times New Roman', Times, serif;
            line-height: 1.75;
            color: #333;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .doc-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 28px 32px;
        }

        .doc-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .doc-subtitle {
            text-align: center;
            font-size: 14px;
            margin-bottom: 30px;
            color: #666;
        }

        .doc-clause {
            margin-bottom: 24px;
        }

        .doc-clause-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #1a1a1a;
        }

        .doc-clause-content {
            font-size: 12px;
            text-align: justify;
            white-space: pre-wrap;
            word-wrap: break-word;
            line-height: 1.8;
            color: #333;
        }

        .doc-signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            padding-top: 30px;
            border-top: 1px solid #ddd;
        }

        .doc-signature-box {
            text-align: center;
            width: 45%;
        }

        .doc-signature-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 20px;
        }

        .doc-signature-name {
            font-size: 12px;
            font-weight: bold;
            margin-top: 5px;
        }

        .doc-footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #888;
        }

        @media print {
            body {
                padding: 0;
                background: white;
            }
            
            .doc-container {
                box-shadow: none;
                max-width: none;
                width: 100%;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="doc-container">
        <h1 class="doc-title">{{ $contract->name }}</h1>
        <p class="doc-subtitle">Contrato entre {{ $user->name }} y PrestaKash Soluciones Financieras S.A.C.</p>

        @foreach($renderedClauses as $clause)
            <div class="doc-clause">
                @if($clause['title'])
                    <h2 class="doc-clause-title">{{ $clause['title'] }}</h2>
                @endif
                <div class="doc-clause-content">
                    {!! $clause['content'] !!}
                </div>
            </div>
        @endforeach

        <div class="doc-signature">
            <div class="doc-signature-box">
                <div class="doc-signature-label">_________________________</div>
                <p class="doc-signature-name">{{ $user->name }}</p>
                <p class="doc-signature-label" style="margin-top: 5px;">DNI: {{ $user->dni }}</p>
            </div>
            <div class="doc-signature-box">
                <div class="doc-signature-label">_________________________</div>
                <p class="doc-signature-name">Juan Carlos Pérez García</p>
                <p class="doc-signature-label" style="margin-top: 5px;">Representante Legal</p>
            </div>
        </div>

        <div class="doc-footer">
            PrestaKash Soluciones Financieras S.A.C. | RUC: 20601234567 | Av. Central 456, Oficina 302, Lima
        </div>
    </div>
</body>
</html>